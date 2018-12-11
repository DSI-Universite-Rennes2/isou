<?php

/**
  * Initialise le schéma de données d'Isou.
  */

use Phinx\Migration\AbstractMigration;

/**
  * Classe de migration pour Phinx
  */
class Initialisation extends AbstractMigration {
    /**
      * Modifie la structure du schéma de la base de données.
      *
      * @throws Exception if any errors occur.
      *
      * @return void
      */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Initialisation Phinx'.PHP_EOL;

        // Configuration table.
        $this->setup_configuration();

        // Menus table.
        $this->setup_menus();

        // Plugins table.
        $this->setup_plugins();

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
                    'author' => '',
                    'last_modification' => '1970-01-01T00:00:00',
                ),
            );
        $table->insert($rows);
        $table->saveData();
    }

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

    public function setup_configuration() {
        echo PHP_EOL.' **  Table de configuration...'.PHP_EOL;

        // Backup old "configuration" table.
        if ($this->hasTable('configuration')) {
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
                    'key' => 'authentification_cas_admin_usernames',
                    'value' => '',
                    'type' => 'array',
                ),
                array(
                    'key' => 'authentification_cas_enabled',
                    'value' => 0,
                    'type' => 'string',
                ),
                array(
                    'key' => 'authentification_manual_enabled',
                    'value' => 0,
                    'type' => 'string',
                ),
                array(
                    'key' => 'authentification_manual_password',
                    'value' => '',
                    'type' => 'string',
                ),
                array(
                    'key' => 'authentification_manual_path',
                    'value' => '',
                    'type' => 'string',
                ),
                array(
                    'key' => 'last_check_update',
                    'value' => 0,
                    'type' => 'datetime',
                ),
                array(
                    'key' => 'last_cron_update',
                    'value' => 0,
                    'type' => 'datetime',
                ),
                array(
                    'key' => 'last_daily_cron_update',
                    'value' => 0,
                    'type' => 'datetime',
                ),
                array(
                    'key' => 'last_update',
                    'value' => 0,
                    'type' => 'datetime',
                ),
                array(
                    'key' => 'menu_default',
                    'value' => 'actualite',
                    'type' => 'string',
                ),
                array(
                    'key' => 'notification_enabled',
                    'value' => 0,
                    'type' => 'string',
                ),
                array(
                    'key' => 'notification_hour',
                    'value' => '06:00',
                    'type' => 'string',
                ),
                array(
                    'key' => 'notification_receivers',
                    'value' => '',
                    'type' => 'array',
                ),
                array(
                    'key' => 'notification_sender',
                    'value' => '',
                    'type' => 'string',
                ),
                array(
                    'key' => 'site_header',
                    'value' => 'ISOU : État des services numériques offerts par l\'Université',
                    'type' => 'string',
                ),
                array(
                    'key' => 'site_name',
                    'value' => 'Isou',
                    'type' => 'string',
                ),
                array(
                    'key' => 'theme',
                    'value' => 'bootstrap',
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

    public function setup_events() {
        echo PHP_EOL.' **  Table des évènements...'.PHP_EOL;

        // Backup old "events" table.
        if ($this->hasTable('events')) {
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
            ->addColumn('enddate', 'string')
            ->addColumn('state', 'integer')
            ->addColumn('type', 'integer')
            ->addColumn('period', 'integer')
            ->addColumn('ideventdescription', 'integer')
            ->addColumn('idservice', 'integer')
            ->addIndex(array('ideventdescription', 'idservice'))
            ->addIndex(array('ideventdescription'))
            ->addIndex(array('idservice'))
            ->create();
    }

    public function setup_events_descriptions() {
        echo PHP_EOL.' **  Table des descriptions des évènements...'.PHP_EOL;

        // Backup old "events_description" table.
        if ($this->hasTable('events_description')) {
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

    public function setup_menus() {
        echo PHP_EOL.' **  Table des menus...'.PHP_EOL;

        // Create "menus" table.
        echo ' ==   - Crée la table "menus".'.PHP_EOL;
        $table = $this->table('menus');
        $table->addColumn('label', 'string')
            ->addColumn('title', 'string')
            ->addColumn('url', 'string')
            ->addColumn('model', 'string')
            ->addColumn('type', 'string')
            ->addColumn('position', 'integer')
            ->addColumn('active', 'integer')
            ->create();

        // Insert "menus" data.
        echo ' ==   - Insère les données dans la table "menus".'.PHP_EOL;
        $rows = array(
                array(
                    'label' => 'actualité',
                    'title' => 'afficher par actualité',
                    'url' => 'actualite',
                    'model' => '/php/public/news.php',
                    'type' => 'guest',
                    'position' => 1,
                    'active' => 1,
                ),
                array(
                    'label' => 'liste',
                    'title' => 'afficher la liste des services',
                    'url' => 'liste',
                    'model' => '/php/public/list.php',
                    'type' => 'guest',
                    'position' => 2,
                    'active' => 1,
                ),
                array(
                    'label' => 'tableau',
                    'title' => 'afficher le tableau des évènements',
                    'url' => 'tableau',
                    'model' => '/php/public/board.php',
                    'type' => 'guest',
                    'position' => 3,
                    'active' => 1,
                ),
                array(
                    'label' => 'journal',
                    'title' => 'afficher le journal d\'évènements',
                    'url' => 'journal',
                    'model' => '/php/public/journal.php',
                    'type' => 'guest',
                    'position' => 4,
                    'active' => 1,
                ),
                array(
                    'label' => 'calendrier',
                    'title' => 'afficher le calendrier des évènements',
                    'url' => 'calendrier',
                    'model' => '/php/public/calendar.php',
                    'type' => 'guest',
                    'position' => 5,
                    'active' => 1,
                ),
                array(
                    'label' => 'contact',
                    'title' => 'nous contacter',
                    'url' => 'contact',
                    'model' => '/php/public/contact.php',
                    'type' => 'guest',
                    'position' => 6,
                    'active' => 1,
                ),
                array(
                    'label' => 'flux rss',
                    'title' => 's\'abonner au flux rss',
                    'url' => 'rss',
                    'model' => '/php/public/rss_config.php',
                    'type' => 'guest',
                    'position' => 7,
                    'active' => 1,
                ),
                array(
                    'label' => 'évènements',
                    'title' => 'ajouter un évenement',
                    'url' => 'evenements',
                    'model' => '/php/events/index.php',
                    'type' => 'admin',
                    'position' => 1,
                    'active' => 1,
                ),
                array(
                    'label' => 'annonce',
                    'title' => 'ajouter une annonce',
                    'url' => 'annonce',
                    'model' => '/php/announcement/index.php',
                    'type' => 'admin',
                    'position' => 2,
                    'active' => 1,
                ),
                array(
                    'label' => 'statistiques',
                    'title' => 'afficher les statistiques',
                    'url' => 'statistiques',
                    'model' => '/php/history/index.php',
                    'type' => 'admin',
                    'position' => 3,
                    'active' => 1,
                ),
                array(
                    'label' => 'services',
                    'title' => 'ajouter un service',
                    'url' => 'services',
                    'model' => '/php/services/index.php',
                    'type' => 'admin',
                    'position' => 4,
                    'active' => 1,
                ),
                array(
                    'label' => 'dépendances',
                    'title' => 'ajouter une dépendance',
                    'url' => 'dependances',
                    'model' => '/php/dependencies/index.php',
                    'type' => 'admin',
                    'position' => 5,
                    'active' => 1,
                ),
                array(
                    'label' => 'catégories',
                    'title' => 'ajouter une catégorie',
                    'url' => 'categories',
                    'model' => '/php/categories/index.php',
                    'type' => 'admin',
                    'position' => 6,
                    'active' => 1,
                ),
                array(
                    'label' => 'configuration',
                    'title' => 'configurer l\'application',
                    'url' => 'configuration',
                    'model' => '/php/settings/index.php',
                    'type' => 'admin',
                    'position' => 7,
                    'active' => 1,
                ),
                array(
                    'label' => 'aide',
                    'title' => 'afficher l\'aide',
                    'url' => 'aide',
                    'model' => '/php/help/index.php',
                    'type' => 'admin',
                    'position' => 8,
                    'active' => 1,
                ),
            );
        $table->insert($rows);
        $table->saveData();
    }

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
            );
        $table->insert($rows);
        $table->saveData();
    }

    public function setup_services() {
        echo PHP_EOL.' **  Table des services...'.PHP_EOL;

        // Backup old "services" table.
        if ($this->hasTable('services')) {
            echo ' ==   - Sauvegarde l\'ancienne table "services".'.PHP_EOL;
            $table = $this->table('services');
            $table->rename('services_old');
        }

        // Create "services" table.
        echo ' ==   - Crée la table "services".'.PHP_EOL;
        $table = $this->table('services');
        $table->addColumn('name', 'string')
            ->addColumn('url', 'string')
            ->addColumn('state', 'integer')
            ->addColumn('comment', 'string')
            ->addColumn('enable', 'integer')
            ->addColumn('visible', 'integer')
            ->addColumn('locked', 'integer')
            ->addColumn('rsskey', 'integer')
            ->addColumn('timemodified', 'string')
            ->addColumn('idplugin', 'integer')
            ->addColumn('idcategory', 'integer')
            ->addIndex(array('rsskey', 'idplugin', 'idcategory'))
            ->addIndex(array('idcategory'))
            ->addIndex(array('idplugin'))
            ->addIndex(array('rsskey'), array('unique' => true))
            ->addIndex(array('name', 'idplugin'), array('unique' => true))
            ->create();
    }

    public function setup_states() {
        echo PHP_EOL.' **  Tables des états des services...'.PHP_EOL;

        // Drop old "states" table.
        if ($this->hasTable('states')) {
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
}
