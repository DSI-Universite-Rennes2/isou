<?php

use Phinx\Migration\AbstractMigration;

class Installation extends AbstractMigration
{
	/**
	* Migrate Up.
	*/
	public function up()
	{
		$now = strftime('%FT%T');


		// Announcement table.
		echo ' ==  Announcement table...'.PHP_EOL;

		// Create "announcement" table.
		echo ' ==   - Create "announcement" table.'.PHP_EOL;
		$table = $this->table('announcement');
		$table->addColumn('message', 'string')
			->addColumn('visible', 'integer')
			->addColumn('author', 'string')
			->addColumn('last_modification', 'string')
			->save();

		// Insert "announcement" data.
		echo ' ==   - Insert "announcement" data.'.PHP_EOL;
		$rows = [
			[
			'message' => '',
			'visible' => 0,
			'author' => '',
			'last_modification' => '1970-01-01T00:00:00',
			]
		];
		foreach ($rows as $row) {
			$table->insert($row);
		}
		$table->saveData();


		// Categories table.
		echo ' ==  Categories table...'.PHP_EOL;

		// Backup old "categories" table.
		if ($this->hasTable('categories')) {
			echo ' ==   - Backup old "categories" table.'.PHP_EOL;
			$table = $this->table('categories');
			$table->rename('categories_old');
		}

		// Create "categories" table.
		echo ' ==   - Create "categories" table.'.PHP_EOL;
		$table = $this->table('categories');
		$table->addColumn('name', 'string')
			->addColumn('position', 'integer')
			->save();


		// Configuration table.
		echo ' ==  Configuration table...'.PHP_EOL;

		// Backup old "configuration" table.
		if ($this->hasTable('configuration')) {
			echo ' ==   - Backup old "configuration" table.'.PHP_EOL;
			$table = $this->table('configuration');
			$table->rename('configuration_old');
		}

		// Create "configuration" table.
		echo ' ==   - Create "configuration" table.'.PHP_EOL;
		$table = $this->table('configuration', array('id' => false, 'primary_key' => array('key')));
		$table->addColumn('key', 'string')
			->addColumn('value', 'string')
			->save();

		// Insert "configuration" data.
		echo ' ==   - Insert "configuration" data.'.PHP_EOL;
		$rows = [
			['key' => 'authentification_cas_admin_usernames', 'value' => ''],
			['key' => 'authentification_cas_enabled', 'value' => 0],
			['key' => 'authentification_manual_enabled', 'value' => 0],
			['key' => 'authentification_manual_password', 'value' => ''],
			['key' => 'authentification_manual_path', 'value' => ''],
			['key' => 'last_check_update', 'value' => 0],
			['key' => 'last_cron_update', 'value' => 0],
			['key' => 'last_daily_cron_update', 'value' => 0],
			['key' => 'last_update', 'value' => 0],
			['key' => 'last_weekly_cron_update', 'value' => 0],
			['key' => 'last_yearly_cron_update', 'value' => 0],
			['key' => 'menu_default', 'value' => 'actualite'],
			['key' => 'nagios_statusdat_enable', 'value' => 0],
			['key' => 'nagios_statusdat_path', 'value' => ''],
			['key' => 'notification_enabled', 'value' => 0],
			['key' => 'notification_hour', 'value' => '06:00'],
			['key' => 'notification_receivers', 'value' => ''],
			['key' => 'notification_sender', 'value' => ''],
			['key' => 'shinken_thruk_enable', 'value' => 0],
			['key' => 'shinken_thruk_password', 'value' => ''],
			['key' => 'shinken_thruk_path', 'value' => ''],
			['key' => 'shinken_thruk_username', 'value' => ''],
			['key' => 'site_header', 'value' => 'ISOU : État des services numériques offerts par l\'université'],
			['key' => 'site_name', 'value' => 'Isou'],
			['key' => 'theme', 'value' => 'bootstrap'],
			['key' => 'tolerance', 'value' => 120],
			['key' => 'version', 'value' => '2.0.0 alpha'],
		];
		foreach ($rows as $row) {
			$table->insert($row);
		}
		$table->saveData();


		// Contact table.
		echo ' ==  Contact table...'.PHP_EOL;

		// Create "contact" table.
		echo ' ==   - Create "contact" table.'.PHP_EOL;
		$table = $this->table('contact');
		$table->addColumn('message', 'string')
			->save();

		// Insert "contact" data.
		echo ' ==   - Insert "contact" data.'.PHP_EOL;
		$table->insert(['message' => '']);
		$table->saveData();


		// Dependencies_groups table.
		echo ' ==  Dependencies_groups table...'.PHP_EOL;

		// Create "dependencies_groups" table.
		echo ' ==   - Create "dependencies_groups" table.'.PHP_EOL;
		$table = $this->table('dependencies_groups');
		$table->addColumn('name', 'string')
			->addColumn('redundant', 'integer')
			->addColumn('groupstate', 'integer')
			->addColumn('idservice', 'integer')
			->addColumn('idmessage', 'integer')
			->addIndex(array('idservice', 'idmessage'))
			->addIndex(array('idmessage'))
			->addIndex(array('idservice'))
			->save();


		// Dependencies_groups_content table.
		echo ' ==  Dependencies_groups_content table...'.PHP_EOL;

		// Create "dependencies_groups_content" table.
		echo ' ==   - Create "dependencies_groups_content" table.'.PHP_EOL;
		$table = $this->table('dependencies_groups_content', array('id' => false, 'primary_key' => array('idgroup', 'idservice', 'servicestate')));
		$table->addColumn('idgroup', 'integer')
			->addColumn('idservice', 'integer')
			->addColumn('servicestate', 'integer')
			// ->addIndex(array('idgroup', 'idservice'), array('unique' => true))
			->save();


		// Dependencies_messages table.
		echo ' ==  Dependencies_messages table...'.PHP_EOL;

		// Create "dependencies_messages" table.
		echo ' ==   - Create "dependencies_messages" table.'.PHP_EOL;
		$table = $this->table('dependencies_messages');
		$table->addColumn('message', 'string')
			->save();


		// Events table.
		echo ' ==  Events table...'.PHP_EOL;

		// Backup old "categories" table.
		if ($this->hasTable('events')) {
			echo ' ==   - Backup old "events" table.'.PHP_EOL;
			$table = $this->table('events');
			$table->rename('events_old');

			echo ' ==   - Backup old "events_isou" table.'.PHP_EOL;
			$table = $this->table('events_isou');
			$table->rename('events_isou_old');

			echo ' ==   - Backup old "events_nagios" table.'.PHP_EOL;
			$table = $this->table('events_nagios');
			$table->rename('events_nagios_old');
		}

		// Create "events" table.
		echo ' ==   - Create "events" table.'.PHP_EOL;
		$table = $this->table('events');
		$table->addColumn('begindate', 'string')
			->addColumn('enddate', 'string')
			->addColumn('state', 'integer')
			->addColumn('type', 'integer')
			->addColumn('period', 'integer')
			->addColumn('ideventdescription', 'integer')
			->addColumn('idservice', 'integer')
			->addIndex(array('ideventdescription', 'idservice'))
			->addIndex(array('ideventdescription'))
			->addIndex(array('idservice'))
			->save();


		// Events_descriptions table.
		echo ' ==  Events_descriptions table...'.PHP_EOL;

		// Backup old "events_description" table.
		if ($this->hasTable('events_description')) {
			echo ' ==   - Backup old "events_description" table.'.PHP_EOL;
			$table = $this->table('events_description');
			$table->rename('events_description_old');
		}

		// Create "events_descriptions" table.
		echo ' ==   - Create "events_descriptions" table.'.PHP_EOL;
		$table = $this->table('events_descriptions');
		$table->addColumn('description', 'string')
			->addColumn('autogen', 'integer')
			->save();


		// Menus table.
		echo ' ==  Menus table...'.PHP_EOL;

		// Create "menus" table.
		echo ' ==   - Create "menus" table.'.PHP_EOL;
		$table = $this->table('menus');
		$table->addColumn('label', 'string')
			->addColumn('title', 'string')
			->addColumn('url', 'string')
			->addColumn('model', 'string')
			->addColumn('position', 'integer')
			->addColumn('active', 'integer')
			->save();

		// Insert "menus" data.
		echo ' ==   - Insert "menus" data.'.PHP_EOL;
		$rows = [
			[
			'label'    => 'actualité',
			'title'    => 'afficher par actualité',
			'url'    => 'actualite',
			'model'    => '/php/public/news.php',
			'position'    => 1,
			'active'    => 1,
			],
			[
			'label'    => 'liste',
			'title'    => 'afficher la liste des services',
			'url'    => 'liste',
			'model'    => '/php/public/list.php',
			'position'    => 2,
			'active'    => 1,
			],
			[
			'label'    => 'tableau',
			'title'    => 'afficher le tableau des évènements',
			'url'    => 'tableau',
			'model'    => '/php/public/board.php',
			'position'    => 3,
			'active'    => 1,
			],
			[
			'label'    => 'journal',
			'title'    => 'afficher le journal d\'évènements',
			'url'    => 'journal',
			'model'    => '/php/public/journal.php',
			'position'    => 4,
			'active'    => 1,
			],
			[
			'label'    => 'calendrier',
			'title'    => 'afficher le calendrier des évènements',
			'url'    => 'calendrier',
			'model'    => '/php/public/calendar.php',
			'position'    => 5,
			'active'    => 1,
			],
			[
			'label'    => 'contact',
			'title'    => 'nous contacter',
			'url'    => 'contact',
			'model'    => '/php/public/contact.php',
			'position'    => 6,
			'active'    => 1,
			],
			[
			'label'    => 'flux rss',
			'title'    => 's\'abonner au flux rss',
			'url'    => 'rss',
			'model'    => '/php/public/rss_config.php',
			'position'    => 7,
			'active'    => 1,
			],
		];
		foreach($rows as $row){
			$table->insert($row);
		}
		$table->saveData();


		// Services table.
		echo ' ==  Services table...'.PHP_EOL;

		// Backup old "services" table.
		if ($this->hasTable('services')) {
			echo ' ==   - Backup old "services" table.'.PHP_EOL;
			$table = $this->table('services');
			$table->rename('services_old');
		}

		// Create "services" table.
		echo ' ==   - Create "services" table.'.PHP_EOL;
		$table = $this->table('services');
		$table->addColumn('name', 'string')
			->addColumn('url', 'string')
			->addColumn('state', 'integer')
			->addColumn('comment', 'string')
			->addColumn('enable', 'integer')
			->addColumn('visible', 'integer')
			->addColumn('locked', 'integer')
			->addColumn('rsskey', 'integer')
			->addColumn('idtype', 'integer')
			->addColumn('idcategory', 'integer')
			->addIndex(array('rsskey', 'idtype', 'idcategory'))
			->addIndex(array('idcategory'))
			->addIndex(array('idtype'))
			// ->addIndex(array('rsskey'), array('unique' => true))
			// ->addIndex(array('name', 'idtype'), array('unique' => true))
			->save();


		// States table.
		echo ' ==  States table...'.PHP_EOL;

		// Drop old "states" table.
		if ($this->hasTable('states')) {
			echo ' ==   - Drop old "states" table.'.PHP_EOL;
			$this->execute('DROP TABLE states');
		}

		// Create "states" table.
		echo ' ==   - Create "states" table.'.PHP_EOL;
		$table = $this->table('states');
		$table->addColumn('name', 'string')
			->addColumn('title', 'string')
			->addColumn('alternative_text', 'string')
			->addColumn('image', 'string')
			->save();

		// Insert "states" data.
		echo ' ==   - Insert "states" data.'.PHP_EOL;
		$rows = [
			[
			'id' => 0,
			'name' => 'ok',
			'title' => 'Service en fonctionnement',
			'alternative_text' => 'Service en fonctionnement',
			'image' => 'flag_green.gif',
			],
			[
			'id' => 1,
			'name' => 'warning',
			'title' => 'Service instable ou indisponible',
			'alternative_text' => 'Service instable ou indisponible',
			'image' => 'flag_orange.gif',
			],
			[
			'id' => 2,
			'name' => 'critical',
			'title' => 'Service indisponible',
			'alternative_text' => 'Service indisponible',
			'image' => 'flag_red.gif',
			],
			[
			'id' => 3,
			'name' => 'unknown',
			'title' => 'Etat du service non connu',
			'alternative_text' => 'Etat du service non connu',
			'image' => 'flag_blue.gif',
			],
			[
			'id' => 4,
			'name' => 'closed',
			'title' => 'Service fermé',
			'alternative_text' => 'Service fermé',
			'image' => 'flag_white.gif',
			],
		];
		foreach($rows as $row){
			$table->insert($row);
		}
		$table->saveData();
	}

	/**
	* Migrate Down.
	*/
	public function down()
	{

	}
}
