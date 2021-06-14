<?php
/**
 * Migre le schéma de données d'Isou en version 2.0.0.
 */

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Classe de migration pour Phinx
 */
class Upgrade200 extends AbstractMigration {
    /**
     * Modifie la structure du schéma de la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Migration du schéma en version 2.0.0.'.PHP_EOL;

        // Configuration table.
        $this->setup_configuration();

        // Announcement table.
        $this->setup_announcement();

        // Categories table.
        $this->setup_categories();

        // Services table.
        $this->setup_services();

        // Events table.
        $this->setup_events();

        // Dependencies table.
        $this->setup_dependencies();

        // Statistics table.
        $this->setup_statistics();
    }

    /**
     * Migre les anciennes données de la table announcement.
     *
     * @return void
     */
    public function setup_announcement() {
        if ($this->hasTable('annonce') === true) {
            echo PHP_EOL.' **  Table des annonces...'.PHP_EOL;

            echo ' ==   - Supprime les données de la table "announcement".'.PHP_EOL;
            $this->execute('DELETE FROM announcement');

            echo ' ==   - Migre les données dans la table "announcement".'.PHP_EOL;
            $table = $this->table('announcement');
            $rows = $this->query('SELECT * FROM annonce');
            foreach ($rows as $row) {
                $data = array(
                    'message' => $row['message'],
                    'visible' => $row['afficher'],
                    'author' => 'isou',
                    'last_modification' => strftime('%FT%T'),
                );
                $table->insert($data);
            }
            $table->saveData();

            echo ' ==   - Supprime l\'ancienne table "annonce".'.PHP_EOL;
            $this->dropTable('annonce');
        }
    }

    /**
     * Migre les anciennes données de la table categories.
     *
     * @return void
     */
    public function setup_categories() {
        if ($this->hasTable('categories_old') === true) {
            echo PHP_EOL.' **  Table des catégories des services...'.PHP_EOL;

            echo ' ==   - Migre les données dans la table "categories".'.PHP_EOL;
            $table = $this->table('categories');
            $rows = $this->query('SELECT * FROM categories_old');
            foreach ($rows as $row) {
                $data = array(
                    'id' => $row['idCategory'],
                    'name' => $row['name'],
                    'position' => $row['position'],
                );
                $table->insert($data);
            }
            $table->saveData();

            echo ' ==   - Supprime l\'ancienne table "categories_old".'.PHP_EOL;
            $this->dropTable('categories_old');
        }
    }

    /**
     * Migre les anciennes données de la table configuration.
     *
     * @return void
     */
    public function setup_configuration() {
        if ($this->hasTable('configuration_old') === true) {
            echo PHP_EOL.' **  Table de configuration...'.PHP_EOL;

            echo ' ==   - Migre les données dans la table "configuration".'.PHP_EOL;
            $connection = $this->getAdapter()->getConnection();
            $statement = $connection->prepare('UPDATE configuration SET value = :value WHERE key = :key');

            $rows = $this->query('SELECT * FROM configuration_old');
            foreach ($rows as $row) {
                $data = array(
                    ':key' => $row['key'],
                    ':value' => $row['value'],
                );

                switch ($row['key']) {
                    case 'admin_mails':
                        $data[':key'] = 'report_receiver';
                        $data[':value'] = '';

                        $admin_mails = json_decode($row['value']);
                        if (isset($admin_mails[0]) === true && filter_var($admin_mails[0], FILTER_VALIDATE_EMAIL) !== false) {
                            $data[':value'] = $admin_mails[0];
                        }
                        break;
                    case 'daily_cron_hour':
                        $data[':key'] = 'report_hour';
                        break;
                    case 'last_check_update':
                    case 'last_cron_update':
                    case 'last_update':
                        $data[':value'] = strftime('%FT%T', $row['value']);
                        break;
                    case 'last_daily_cron_update':
                        $data[':key'] = 'last_daily_report';
                        $data[':value'] = strftime('%FT%T', $row['value']);
                        break;
                    case 'local_mail':
                        $data[':key'] = 'report_sender';
                        break;
                    case 'version':
                        $data[':value'] = '2.0.0';
                        break;
                    case 'admin_users':
                    case 'auto_backup':
                    case 'ip_local':
                    case 'ip_service':
                    case 'local_password':
                        continue 2;
                }

                $statement->execute($data);
            }

            // Force l'utilisation de Nagios.
            $statement = $connection->prepare('UPDATE plugins SET active = 1 WHERE codename = "nagios"');
            $statement->execute();

            echo ' ==   - Supprime l\'ancienne table "configuration_old".'.PHP_EOL;
            $this->dropTable('configuration_old');
        }
    }

    /**
     * Migre les anciennes données de la table dependencies.
     *
     * @return void
     */
    public function setup_dependencies() {
        if ($this->hasTable('dependencies') === true) {
            echo PHP_EOL.' **  Tables des dépendances...'.PHP_EOL;

            // Migrate "dependencies" data.
            echo ' ==   - Migre les données dans la table "dependencies".'.PHP_EOL;
            $dependencies_groups = array();
            $dependencies_groups_contents = array();
            $dependencies_messages = array();

            $rows = $this->query('SELECT * FROM dependencies');
            foreach ($rows as $row) {
                $idmessage = array_search($row['message'], $dependencies_messages);
                if ($idmessage === false) {
                    $idmessage = count($dependencies_messages);
                    $dependencies_messages[$idmessage] = array('message' => $row['message']);
                }

                $group_key = $row['idService'].'-'.$row['newStateForChild'];
                if (isset($dependencies_groups[$group_key]) === false) {
                    $dependencies_groups[$group_key] = array(
                        'id' => count($dependencies_groups) + 1,
                        'name' => 'Groupe non redondé',
                        'redundant' => 0,
                        'groupstate' => $row['newStateForChild'],
                        'idservice' => $row['idService'],
                        'idmessage' => $idmessage + 1,
                    );
                }

                $key = $dependencies_groups[$group_key]['id'].'-'.$row['idServiceParent'].'-'.$row['stateOfParent'];
                $dependencies_groups_contents[$key] = array(
                    'idgroup' => $dependencies_groups[$group_key]['id'],
                    'idservice' => $row['idServiceParent'],
                    'servicestate' => $row['stateOfParent'],
                );
            }

            // Migrate "dependencies_groups" data.
            echo ' ==   - Migre les données dans la table "dependencies_groups".'.PHP_EOL;
            $table = $this->table('dependencies_groups');
            $table->insert(array_values($dependencies_groups));
            $table->saveData();

            // Migrate "dependencies_groups_content" data.
            echo ' ==   - Migre les données dans la table "dependencies_groups_content".'.PHP_EOL;
            $table = $this->table('dependencies_groups_content');
            $table->insert(array_values($dependencies_groups_contents));
            $table->saveData();

            // Migrate "dependencies_messages" data.
            echo ' ==   - Migre les données dans la table "dependencies_messages".'.PHP_EOL;
            $table = $this->table('dependencies_messages');
            $table->insert(array_values($dependencies_messages));
            $table->saveData();

            // Drop old "dependencies" table.
            echo ' ==   - Supprime l\'ancienne table "dependencies".'.PHP_EOL;
            $this->dropTable('dependencies');
        }
    }

    /**
     * Migre les anciennes données de la table events.
     *
     * @return void
     */
    public function setup_events() {
        if ($this->hasTable('events_old') === true) {
            echo PHP_EOL.' **  Table des évènements...'.PHP_EOL;

            // Migrate "events" data.
            echo ' ==   - Migre les données dans la table "events".'.PHP_EOL;
            $table = $this->table('events');
            $rows = $this->query('SELECT * FROM events_old eo JOIN events_isou_old eio ON eo.idEvent = eio.idEvent WHERE eo.typeEvent = 0');
            foreach ($rows as $row) {
                if (empty($row['endDate']) === true) {
                    $enddate = null;
                } else {
                    $enddate = $row['endDate'].':00';
                }

                if ($row['isScheduled'] === '2') {
                    $period = $row['period'];
                } else {
                    $period = 0;
                }

                if ($row['isScheduled'] === '3') {
                    $state = 4;
                } else {
                    $state = 2;
                }

                $data = array(
                    'startdate' => $row['beginDate'].':00',
                    'enddate' => $enddate,
                    'state' => $state,
                    'type' => $row['isScheduled'], // 0 = unscheduled events, 1 = scheduled, 2 = regular, 3 = closed.
                    'period' => $period,
                    'ideventdescription' => $row['idEventDescription'],
                    'idservice' => $row['idService'],
                );
                $table->insert($data);
            }

            $rows = $this->query('SELECT * FROM events_old eo JOIN events_nagios_old eno ON eo.idEvent = eno.idEvent WHERE eo.typeEvent = 1');
            foreach ($rows as $row) {
                if (empty($row['endDate']) === true) {
                    $enddate = null;
                } else {
                    $enddate = $row['endDate'].':00';
                }

                $data = array(
                    'startdate' => $row['beginDate'].':00',
                    'enddate' => $enddate,
                    'state' => $row['state'],
                    'type' => 0, // 0 = unscheduled events.
                    'period' => 0,
                    'ideventdescription' => 0,
                    'idservice' => $row['idService'],
                );
                $table->insert($data);
            }
            $table->saveData();

            // Migrate "events_descriptions" data.
            echo ' ==   - Migre les données dans la table "events_descriptions".'.PHP_EOL;
            $table = $this->table('events_descriptions');
            $rows = $this->query('SELECT * FROM events_description_old');
            foreach ($rows as $row) {
                $data = array(
                    'id' => $row['idEventDescription'],
                    'description' => $row['description'],
                    'autogen' => $row['autogen'],
                );
                $table->insert($data);
            }
            $table->saveData();

            // Drop old "events_old" table.
            echo ' ==   - Supprime l\'ancienne table "events_old".'.PHP_EOL;
            $this->dropTable('events_old');

            // Drop old "events_isou_old" table.
            echo ' ==   - Supprime l\'ancienne table "events_isou_old".'.PHP_EOL;
            $this->dropTable('events_isou_old');

            // Drop old "events_nagios_old" table.
            echo ' ==   - Supprime l\'ancienne table "events_nagios_old".'.PHP_EOL;
            $this->dropTable('events_nagios_old');

            // Drop old "events_description_old" table.
            echo ' ==   - Supprime l\'ancienne table "events_description_old".'.PHP_EOL;
            $this->dropTable('events_description_old');

            // Drop old "events_info" table.
            echo ' ==   - Supprime l\'ancienne table "events_info".'.PHP_EOL;
            $this->dropTable('events_info');
        }
    }

    /**
     * Migre les anciennes données de la table services.
     *
     * @return void
     */
    public function setup_services() {
        if ($this->hasTable('services_old') === true) {
            echo PHP_EOL.' **  Table des services...'.PHP_EOL;

            // Migrate "services" data.
            echo ' ==   - Migre les données dans la table "services".'.PHP_EOL;
            $table = $this->table('services');
            $rows = $this->query('SELECT * FROM services_old');
            foreach ($rows as $row) {
                if (empty($row['nameForUsers']) === true) {
                    $idplugin = 2; // Plugin Nagios.
                } else {
                    $idplugin = 1; // Plugin Isou.
                    $row['name'] = $row['nameForUsers'];
                }

                if (empty($row['url']) === true) {
                    $row['url'] = null;
                }

                if (empty($row['comment']) === true) {
                    $row['comment'] = null;
                }

                $data = array(
                    'id' => $row['idService'],
                    'name' => $row['name'],
                    'url' => $row['url'],
                    'state' => $row['state'],
                    'comment' => $row['comment'],
                    'enable' => $row['enable'],
                    'visible' => $row['visible'],
                    'locked' => $row['readonly'],
                    'rsskey' => $row['rssKey'],
                    'timemodified' => strftime('%FT%T'),
                    'idplugin' => $idplugin,
                    'idcategory' => $row['idCategory'],
                );
                $table->insert($data);
            }
            $table->saveData();

            // Drop old "services_old" table.
            echo ' ==   - Supprime l\'ancienne table "services_old".'.PHP_EOL;
            $this->dropTable('services_old');
        }
    }

    /**
     * Migre les anciennes données de la table statistics.
     *
     * @return void
     */
    public function setup_statistics() {
        if ($this->hasTable('statistics') === true) {
            echo PHP_EOL.' **  Tables des statistiques...'.PHP_EOL;

            // Drop old "statistics" table.
            echo ' ==   - Supprime l\'ancienne table "statistics".'.PHP_EOL;
            $this->dropTable('statistics');
        }
    }
}
