<?php

/**
 * Migre le schéma de données d'Isou en version 2.0.0.
 */

use Phinx\Migration\AbstractMigration;

/**
 * Classe de migration pour Phinx
 */
class Upgrade200 extends AbstractMigration {
    /**
     * Modifie la structure du schéma de la base de données.
     *
     * @throws Exception if any errors occur.
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
                    'author' => '',
                    'last_modification' => strftime('%FT%T'),
                    );
                $table->insert($data);
            }
            $table->saveData();

            echo ' ==   - Supprime l\'ancienne table "annonce".'.PHP_EOL;
            $this->dropTable('annonce');
        }
    }

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
                    'position' => $row['position']
                    );
                $table->insert($data);
            }
            $table->saveData();

            echo ' ==   - Supprime l\'ancienne table "categories_old".'.PHP_EOL;
            $this->dropTable('categories_old');
        }
    }

    public function setup_configuration() {
        if ($this->hasTable('configuration_old') === true) {
            echo PHP_EOL.' **  Table de configuration...'.PHP_EOL;

            echo ' ==   - Migre les données dans la table "configuration".'.PHP_EOL;
            $connection = $this->getAdapter()->getConnection();
            $statement = $connection->prepare('UPDATE configuration SET value = :value WHERE key = :key');

            $rows = $this->query('SELECT * FROM configuration_old');
            foreach ($rows as $row) {
                $data = array(':key' => $row['key'], ':value' => $row['value']);

                switch ($row['key']) {
                    case 'admin_mails':
                        $data[':key'] = 'notification_receivers';
                        break;
                    case 'admin_users':
                        $data[':key'] = 'authentification_cas_admin_usernames';
                        break;
                    case 'daily_cron_hour':
                        $data[':key'] = 'notification_hour';
                        break;
                    case 'last_check_update':
                    case 'last_cron_update':
                    case 'last_daily_cron_update':
                    case 'last_update':
                        $data[':value'] = strftime('%FT%T', $row['value']);
                        break;
                    case 'local_mail':
                        $data[':key'] = 'notification_sender';
                        break;
                    case 'local_password':
                        $data[':key'] = 'authentification_manual_password';
                        break;
                    case 'version':
                        $data[':value'] = '2.0.0';
                        break;
                    case 'auto_backup';
                    case 'ip_local';
                    case 'ip_service';
                        continue 2;
                }

                $statement->execute($data);
            }

            // Force l'utilisation de Nagios.
            $data = array('key' => 'nagios_statusdat_enable', 'value' => 1);
            $statement->execute($data);
            $data = array('key' => 'nagios_statusdat_path', 'value' => '/var/share/nagios/status.dat');
            $statement->execute($data);

            echo ' ==   - Supprime l\'ancienne table "configuration_old".'.PHP_EOL;
            $this->dropTable('configuration_old');
        }
    }

    public function setup_dependencies() {
        if ($this->hasTable('dependencies') === true) {
            echo PHP_EOL.' **  Tables des dépendances...'.PHP_EOL;

            // Migrate "dependencies" data.
            echo ' ==   - Migre les données dans la table "dependencies".'.PHP_EOL;
            $dependencies_groups = array();
            $dependencies_groups_contents = array();
            $dependencies_messages = array();

            $rows = $this->query('SELECT * FROM dependencies');
            foreach($rows as $row){
                $idmessage = array_search($row['message'], $dependencies_messages);
                if ($idmessage === false) {
                    $idmessage = count($dependencies_messages);
                    $dependencies_messages[$idmessage] = array('message' => $row['message']);
                }

                $group_key = $row['idService'].'-'.$row['newStateForChild'];
                if (isset($dependencies_groups[$group_key]) === false) {
                    $dependencies_groups[$group_key] = [
                        'id' => count($dependencies_groups) + 1,
                        'name' => 'Groupe non redondé',
                        'redundant' => 0,
                        'groupstate' => $row['newStateForChild'],
                        'idservice' => $row['idService'],
                        'idmessage' => $idmessage + 1,
                    ];
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
                    $period = null;
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
                $data = array(
                    'startdate' => $row['beginDate'].':00',
                    'enddate' => $row['endDate'].':00',
                    'state' => $row['state'],
                    'type' => 0, // 0 = unscheduled events.
                    'period' => null,
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

    public function setup_services() {
        if ($this->hasTable('services_old') === true) {
            echo PHP_EOL.' **  Table des services...'.PHP_EOL;

            // Migrate "services" data.
            echo ' ==   - Migre les données dans la table "services".'.PHP_EOL;
            $table = $this->table('services');
            $rows = $this->query('SELECT * FROM services_old');
            foreach ($rows as $row) {
                if (empty($row['nameForUsers']) === true) {
                    $idtype = 2; // Type Nagios.
                } else {
                    $idtype = 1; // Type Isou.
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
                    'idtype' => $idtype,
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

    public function setup_statistics() {
        if ($this->hasTable('statistics') === true) {
            echo PHP_EOL.' **  Tables des statistiques...'.PHP_EOL;

            // Drop old "statistics" table.
            echo ' ==   - Supprime l\'ancienne table "statistics".'.PHP_EOL;
            $this->dropTable('statistics');
        }
    }
}
