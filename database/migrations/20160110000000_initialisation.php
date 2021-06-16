<?php
/**
 * Initialise le schéma de données d'Isou.
 */

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Classe de migration pour Phinx
 */
class Initialisation extends AbstractMigration {
    /**
     * Modifie la structure du schéma de la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Initialisation Phinx'.PHP_EOL;

        // Configuration table.
        $this->setup_configuration();

        // Plugins table.
        $this->setup_plugins();

        // Users table.
        $this->setup_users();

        // Subscriptions table.
        $this->setup_subscriptions();

        // Announcement table.
        $this->setup_announcement();

        // Contact table.
        $this->setup_contact();

        // States table.
        $this->setup_states();

        // Categories table.
        $this->setup_categories();

        // Services table.
        $this->setup_services();

        // Events table.
        $this->setup_events();

        // Events_descriptions table.
        $this->setup_events_descriptions();

        // Dependencies_groups table.
        $this->setup_dependencies_groups();

        // Dependencies_groups_content table.
        $this->setup_dependencies_groups_content();

        // Dependencies_messages table.
        $this->setup_dependencies_messages();
    }

    /**
     * Génère la table announcement.
     *
     * @return void
     */
    public function setup_announcement() {
        echo PHP_EOL.' **  Table des annonces...'.PHP_EOL;

        // Create "announcement" table.
        echo ' ==   - Crée la table "announcement".'.PHP_EOL;
        $table = $this->table('announcement');
        $table->addColumn('message', 'string')
            ->addColumn('visible', 'integer')
            ->addColumn('author', 'string')
            ->addColumn('last_modification', 'string')
            ->create();

        // Insert "announcement" data.
        echo ' ==   - Insère les données dans la table "announcement".'.PHP_EOL;
        $rows = array(
            array(
                'message' => '',
                'visible' => 0,
                'author' => 'isou',
                'last_modification' => '1970-01-01T00:00:00',
            ),
        );
        $table->insert($rows);
        $table->saveData();
    }

    /**
     * Génère la table categories.
     *
     * @return void
     */
    public function setup_categories() {
        echo PHP_EOL.' **  Table des catégories des services...'.PHP_EOL;

        // Backup old "categories" table.
        if ($this->hasTable('categories') === true) {
            echo ' ==   - Sauvegarde l\'ancienne table "categories".'.PHP_EOL;
            $table = $this->table('categories');
            $table->rename('categories_old');
        }

        // Create "categories" table.
        echo ' ==   - Crée la table "categories".'.PHP_EOL;
        $table = $this->table('categories');
        $table->addColumn('name', 'string')
            ->addColumn('position', 'integer')
            ->create();
    }

    /**
     * Génère la table configuration.
     *
     * @return void
     */
    public function setup_configuration() {
        echo PHP_EOL.' **  Table de configuration...'.PHP_EOL;

        // Backup old "configuration" table.
        if ($this->hasTable('configuration') === true) {
            echo ' ==   - Sauvegarde l\'ancienne table "configuration".'.PHP_EOL;
            $table = $this->table('configuration');
            $table->rename('configuration_old');
        }

        // Create "configuration" table.
        echo ' ==   - Crée la table "configuration".'.PHP_EOL;
        $table = $this->table('configuration', array('id' => false, 'primary_key' => array('key')));
        $table->addColumn('key', 'string')
            ->addColumn('value', 'string')
            ->addColumn('type', 'string')
            ->create();

        // Insert "configuration" data.
        echo ' ==   - Insère les données dans la table "configuration".'.PHP_EOL;
        $rows = array(
            array(
                'key' => 'last_check_update',
                'value' => strftime('%FT%T'),
                'type' => 'datetime',
            ),
            array(
                'key' => 'last_cron_update',
                'value' => 0,
                'type' => 'datetime',
            ),
            array(
                'key' => 'last_daily_report',
                'value' => 0,
                'type' => 'datetime',
            ),
            array(
                'key' => 'last_update',
                'value' => strftime('%FT%T'),
                'type' => 'datetime',
            ),
            array(
                'key' => 'menu_default',
                'value' => 'list',
                'type' => 'string',
            ),
            array(
                'key' => 'notifications_enabled',
                'value' => 0,
                'type' => 'string',
            ),
            array(
                'key' => 'report_enabled',
                'value' => 0,
                'type' => 'string',
            ),
            array(
                'key' => 'report_hour',
                'value' => '06:00',
                'type' => 'string',
            ),
            array(
                'key' => 'report_receiver',
                'value' => '',
                'type' => 'string',
            ),
            array(
                'key' => 'report_sender',
                'value' => '',
                'type' => 'string',
            ),
            array(
                'key' => 'site_header',
                'value' => 'État des services numériques',
                'type' => 'string',
            ),
            array(
                'key' => 'site_name',
                'value' => 'ISOU',
                'type' => 'string',
            ),
            array(
                'key' => 'site_url',
                'value' => '',
                'type' => 'string',
            ),
            array(
                'key' => 'theme',
                'value' => 'bootstrap3',
                'type' => 'string',
            ),
            array(
                'key' => 'version',
                'value' => '2.0.0',
                'type' => 'string',
            ),
        );
        $table->insert($rows);
        $table->saveData();
    }

    /**
     * Génère la table contact.
     *
     * @return void
     */
    public function setup_contact() {
        echo PHP_EOL.' **  Table de contact...'.PHP_EOL;

        // Create "contact" table.
        echo ' ==   - Crée la table "contact".'.PHP_EOL;
        $table = $this->table('contact');
        $table->addColumn('message', 'string')
            ->create();

        // Insert "contact" data.
        echo ' ==   - Insère les données dans la table "contact".'.PHP_EOL;
        $table->insert(array('message' => ''));
        $table->saveData();
    }

    /**
     * Génère la table dependencies_groups.
     *
     * @return void
     */
    public function setup_dependencies_groups() {
        echo PHP_EOL.' **  Tables des groupes de dépendances...'.PHP_EOL;

        // Create "dependencies_groups" table.
        echo ' ==   - Crée la table "dependencies_groups".'.PHP_EOL;
        $table = $this->table('dependencies_groups');
        $table->addColumn('name', 'string')
            ->addColumn('redundant', 'integer')
            ->addColumn('groupstate', 'integer')
            ->addColumn('idservice', 'integer')
            ->addColumn('idmessage', 'integer')
            ->addIndex(array('idservice', 'idmessage'))
            ->addIndex(array('idmessage'))
            ->addIndex(array('idservice'))
            ->create();
    }

    /**
     * Génère la table dependencies_groups_content.
     *
     * @return void
     */
    public function setup_dependencies_groups_content() {
        echo PHP_EOL.' **  Tables des contenus des groupes de dépendances...'.PHP_EOL;

        // Create "dependencies_groups_content" table.
        echo ' ==   - Crée la table "dependencies_groups_content".'.PHP_EOL;
        $table = $this->table('dependencies_groups_content');
        $table->addColumn('idgroup', 'integer')
            ->addColumn('idservice', 'integer')
            ->addColumn('servicestate', 'integer')
            ->addIndex(array('idgroup', 'idservice'), array('unique' => true))
            ->create();
    }

    /**
     * Génère la table dependencies_messages.
     *
     * @return void
     */
    public function setup_dependencies_messages() {
        echo PHP_EOL.' **  Tables des messages de dépendances...'.PHP_EOL;

        // Create "dependencies_messages" table.
        echo ' ==   - Crée la table "dependencies_messages".'.PHP_EOL;
        $table = $this->table('dependencies_messages');
        $table->addColumn('message', 'string')
            ->create();

        // Insert "dependencies_messages" data.
        echo ' ==   - Insère les données dans la table "dependencies_messages".'.PHP_EOL;
        $table->insert(array('message' => ''));
        $table->saveData();
    }

    /**
     * Génère la table events.
     *
     * @return void
     */
    public function setup_events() {
        echo PHP_EOL.' **  Table des évènements...'.PHP_EOL;

        // Backup old "events" table.
        if ($this->hasTable('events') === true) {
            echo ' ==   - Sauvegarde l\'ancienne table "events".'.PHP_EOL;
            $table = $this->table('events');
            $table->rename('events_old');

            echo ' ==   - Sauvegarde l\'ancienne table "events_isou".'.PHP_EOL;
            $table = $this->table('events_isou');
            $table->rename('events_isou_old');

            echo ' ==   - Sauvegarde l\'ancienne table "events_nagios".'.PHP_EOL;
            $table = $this->table('events_nagios');
            $table->rename('events_nagios_old');
        }

        // Create "events" table.
        echo ' ==   - Crée la table "events".'.PHP_EOL;
        $table = $this->table('events');
        $table->addColumn('startdate', 'string')
            ->addColumn('enddate', 'string', array('null' => true))
            ->addColumn('state', 'integer')
            ->addColumn('type', 'integer')
            ->addColumn('period', 'integer', array('null' => true))
            ->addColumn('ideventdescription', 'integer')
            ->addColumn('idservice', 'integer')
            ->addIndex(array('ideventdescription', 'idservice'))
            ->addIndex(array('ideventdescription'))
            ->addIndex(array('idservice'))
            ->create();
    }

    /**
     * Génère la table events_descriptions.
     *
     * @return void
     */
    public function setup_events_descriptions() {
        echo PHP_EOL.' **  Table des descriptions des évènements...'.PHP_EOL;

        // Backup old "events_description" table.
        if ($this->hasTable('events_description') === true) {
            echo ' ==   - Sauvegarde l\'ancienne table "events_description".'.PHP_EOL;
            $table = $this->table('events_description');
            $table->rename('events_description_old');
        }

        // Create "events_descriptions" table.
        echo ' ==   - Crée la table "events_descriptions".'.PHP_EOL;
        $table = $this->table('events_descriptions');
        $table->addColumn('description', 'string')
            ->addColumn('autogen', 'integer')
            ->create();
    }

    /**
     * Génère la table plugins.
     *
     * @return void
     */
    public function setup_plugins() {
        echo PHP_EOL.' **  Tables des plugins...'.PHP_EOL;

        // Create "plugins" table.
        echo ' ==   - Crée la table "plugins".'.PHP_EOL;
        $table = $this->table('plugins');
        $table->addColumn('name', 'string')
            ->addColumn('codename', 'string')
            ->addColumn('type', 'string')
            ->addColumn('active', 'integer')
            ->addColumn('version', 'string')
            ->addIndex(array('codename'), array('unique' => true))
            ->addIndex(array('active'))
            ->create();

        // Insert "plugins" data.
        echo ' ==   - Insère les données dans la table "plugins".'.PHP_EOL;
        $rows = array(
            array(
                'id' => 1,
                'name' => 'Isou',
                'codename' => 'isou',
                'type' => 'monitoring',
                'active' => 1,
                'version' => '1.0.0',
            ),
            array(
                'id' => 2,
                'name' => 'Nagios',
                'codename' => 'nagios',
                'type' => 'monitoring',
                'active' => 0,
                'version' => '1.0.0',
            ),
            array(
                'id' => 3,
                'name' => 'Authentification locale',
                'codename' => 'manual',
                'type' => 'authentification',
                'active' => 1,
                'version' => '1.0.0',
            ),
            array(
                'id' => 4,
                'name' => 'Liste',
                'codename' => 'list',
                'type' => 'view',
                'active' => 1,
                'version' => '1.0.0',
            ),
        );
        $table->insert($rows);
        $table->saveData();

        // Create "plugins_settings" table.
        echo ' ==   - Crée la table "plugins_settings".'.PHP_EOL;
        $table = $this->table('plugins_settings');
        $table->addColumn('key', 'string')
            ->addColumn('value', 'string')
            ->addColumn('type', 'string')
            ->addColumn('idplugin', 'string')
            ->addIndex(array('key'))
            ->addIndex(array('key', 'idplugin'), array('unique' => true))
            ->create();

        // Insert "plugins_settings" data.
        echo ' ==   - Insère les données dans la table "plugins_settings".'.PHP_EOL;
        $rows = array(
            array(
                'id' => 1,
                'key' => 'tolerance',
                'value' => '120',
                'type' => 'string',
                'idplugin' => 1,
            ),
            array(
                'id' => 2,
                'key' => 'statusdat_path',
                'value' => '/var/share/nagios/status.dat',
                'type' => 'string',
                'idplugin' => 2,
            ),
            array(
                'id' => 3,
                'key' => 'label',
                'value' => 'Liste',
                'type' => 'string',
                'idplugin' => 4,
            ),
            array(
                'id' => 4,
                'key' => 'route',
                'value' => 'liste',
                'type' => 'string',
                'idplugin' => 4,
            ),
        );
        $table->insert($rows);
        $table->saveData();
    }

    /**
     * Génère la table services.
     *
     * @return void
     */
    public function setup_services() {
        echo PHP_EOL.' **  Table des services...'.PHP_EOL;

        // Backup old "services" table.
        if ($this->hasTable('services') === true) {
            echo ' ==   - Sauvegarde l\'ancienne table "services".'.PHP_EOL;
            $table = $this->table('services');
            $table->rename('services_old');
        }

        // Create "services" table.
        echo ' ==   - Crée la table "services".'.PHP_EOL;
        $table = $this->table('services');
        $table->addColumn('name', 'string')
            ->addColumn('url', 'string', array('null' => true))
            ->addColumn('state', 'integer')
            ->addColumn('comment', 'string', array('null' => true))
            ->addColumn('enable', 'integer')
            ->addColumn('visible', 'integer')
            ->addColumn('locked', 'integer')
            ->addColumn('rsskey', 'integer')
            ->addColumn('timemodified', 'string')
            ->addColumn('idplugin', 'integer')
            ->addColumn('idcategory', 'integer', array('null' => true))
            ->addIndex(array('rsskey', 'idplugin', 'idcategory'))
            ->addIndex(array('idcategory'))
            ->addIndex(array('idplugin'))
            ->addIndex(array('rsskey'), array('unique' => true))
            ->addIndex(array('name', 'idplugin'), array('unique' => true))
            ->create();
    }

    /**
     * Génère la table states.
     *
     * @return void
     */
    public function setup_states() {
        echo PHP_EOL.' **  Tables des états des services...'.PHP_EOL;

        // Drop old "states" table.
        if ($this->hasTable('states') === true) {
            echo ' ==   - Supprime l\'ancienne table "states".'.PHP_EOL;
            $this->execute('DROP TABLE states');
        }

        // Create "states" table.
        echo ' ==   - Crée la table "states".'.PHP_EOL;
        $table = $this->table('states');
        $table->addColumn('name', 'string')
            ->addColumn('title', 'string')
            ->addColumn('alternate_text', 'string')
            ->addColumn('image', 'string')
            ->create();

        // Insert "states" data.
        echo ' ==   - Insère les données dans la table "states".'.PHP_EOL;
        $rows = array(
            array(
                'id' => 0,
                'name' => 'ok',
                'title' => 'Service en fonctionnement',
                'alternate_text' => 'Service en fonctionnement',
                'image' => 'flag_green.gif',
            ),
            array(
                'id' => 1,
                'name' => 'warning',
                'title' => 'Service instable ou indisponible',
                'alternate_text' => 'Service instable ou indisponible',
                'image' => 'flag_orange.gif',
            ),
            array(
                'id' => 2,
                'name' => 'critical',
                'title' => 'Service indisponible',
                'alternate_text' => 'Service indisponible',
                'image' => 'flag_red.gif',
            ),
            array(
                'id' => 3,
                'name' => 'unknown',
                'title' => 'Etat du service non connu',
                'alternate_text' => 'Etat du service non connu',
                'image' => 'flag_blue.gif',
            ),
            array(
                'id' => 4,
                'name' => 'closed',
                'title' => 'Service fermé',
                'alternate_text' => 'Service fermé',
                'image' => 'flag_white.gif',
            ),
        );
        $table->insert($rows);
        $table->saveData();
    }

    /**
     * Génère la table subscriptions.
     *
     * @return void
     */
    public function setup_subscriptions() {
        echo PHP_EOL.' **  Tables des inscriptions aux notifications web...'.PHP_EOL;

        // Create "subscriptions" table.
        echo ' ==   - Crée la table "subscriptions".'.PHP_EOL;
        $table = $this->table('subscriptions');
        $table->addColumn('endpoint', 'string')
            ->addColumn('public_key', 'string')
            ->addColumn('authentification_token', 'string')
            ->addColumn('content_encoding', 'string')
            ->addColumn('lastnotification', 'string', array('null' => true))
            ->addColumn('iduser', 'integer', array('null' => true))
            ->create();
    }

    /**
     * Génère la table users.
     *
     * @return void
     */
    public function setup_users() {
        echo PHP_EOL.' **  Tables des utilisateurs...'.PHP_EOL;

        // Create "users" table.
        echo ' ==   - Crée la table "users".'.PHP_EOL;
        $table = $this->table('users');
        $table->addColumn('authentification', 'string')
            ->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('firstname', 'string')
            ->addColumn('lastname', 'string')
            ->addColumn('email', 'string')
            ->addColumn('admin', 'integer')
            ->addColumn('lastaccess', 'string', array('null' => true))
            ->addColumn('timecreated', 'string')
            ->addIndex(array('authentification', 'username'), array('unique' => true))
            ->create();

        // Insert "users" data.
        echo ' ==   - Insère les données dans la table "users".'.PHP_EOL;
        $rows = array(
            array(
                'id' => 1,
                'authentification' => 'manual',
                'username' => 'isou',
                'password' => '$2y$10$nSpMR6qncUyMSZfjrix5Du7Rzi1k5hCnDMPRse4mvDhWaZzYwnEV.', // Default: isou.
                'firstname' => '',
                'lastname' => 'Misou-Mizou',
                'email' => '',
                'admin' => 1,
                'lastaccess' => null,
                'timecreated' => strftime('%FT%T'),
            ),
        );
        $table->insert($rows);
        $table->saveData();
    }
}
