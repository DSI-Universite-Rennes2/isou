<?php

use Phinx\Migration\AbstractMigration;

class Upgrade200 extends AbstractMigration
{
	/**
	* Migrate Up.
	*/
	public function up()
	{
		$now = strftime('%FT%T');


		// Announcement table.
		if ($this->hasTable('annonce')) {
			echo ' ==  Announcement table...'.PHP_EOL;

			echo ' ==   - Delete "announcement" data.'.PHP_EOL;
			$this->execute('DELETE FROM announcement');

			echo ' ==   - Migrate "announcement" data.'.PHP_EOL;
			$table = $this->table('announcement');
			$rows = $this->query('SELECT * FROM annonce');
			foreach ($rows as $row) {
				$data = [
					'message' => $row['message'],
					'visible' => $row['afficher'],
					'author' => '',
					'last_modification' => $now
				];
				$table->insert($data);
			}
			$table->saveData();

			echo ' ==   - Drop old "announcement" table.'.PHP_EOL;
			$this->dropTable('annonce');
		}


		// Categories table.
		if ($this->hasTable('categories_old')) {
			echo ' ==  Categories table...'.PHP_EOL;

			echo ' ==   - Migrate "categories" data.'.PHP_EOL;
			$table = $this->table('categories');
			$rows = $this->query('SELECT * FROM categories_old');
			foreach ($rows as $row) {
				$data = [
					'id' => $row['idCategory'],
					'name' => $row['name'],
					'position' => $row['position']
				];
				$table->insert($data);
			}
			$table->saveData();

			echo ' ==   - Drop old "categories" table.'.PHP_EOL;
			$this->dropTable('categories_old');
		}


		// Configuration table.
		if ($this->hasTable('configuration_old')) {
			echo ' ==  Configuration table...'.PHP_EOL;

			echo ' ==   - Migrate "configuration" data.'.PHP_EOL;
			$rows = $this->query('SELECT * FROM configuration_old');
			foreach ($rows as $row) {
				$data = array('key' => $row['key'], 'value' => $row['value']);

				switch($row['key']){
					case 'admin_mails':
						$data['key'] = 'notification_receivers';

						break;
					case 'admin_users':
						$data['key'] = 'authentification_cas_admin_usernames';
						break;
					case 'daily_cron_hour':
						$data['key'] = 'notification_hour';
						break;
					case 'local_mail':
						$data['key'] = 'notification_sender';
						break;
					case 'local_password':
						$data['key'] = 'authentification_manual_password';
						break;
					case 'version':
						$data['value'] = '2.0.0 alpha';
						break;
					case 'auto_backup';
					case 'ip_local';
					case 'ip_service';
						continue 2;
				}

				$this->execute('UPDATE configuration SET '.$row['key'].'="'.$row['value'].'" WHERE key="'.$row['key'].'"');
			}

			echo ' ==   - Drop old "configuration" table.'.PHP_EOL;
			$this->dropTable('configuration_old');
		}


		// Dependencies_groups table.
		if ($this->hasTable('dependencies')) {
			echo ' ==  Dependencies table...'.PHP_EOL;

			// Migrate "dependencies" data.
			echo ' ==   - Migrate "dependencies" data.'.PHP_EOL;
			$dependencies_groups = array();
			$dependencies_groups_content = array();
			$dependencies_messages = array();

			$rows = $this->query('SELECT * FROM dependencies');
			foreach($rows as $row){
				$idmessage = array_search($row['message'], $dependencies_messages);
				if ($idmessage === false ){
					$idmessage = count($dependencies_messages);
					$dependencies_messages[$idmessage] = array('message' => $row['message']);
				}

				$group_key = $row['idService'].'-'.$row['newStateForChild'];
				if(!isset($dependencies_groups[$group_key])){
					$dependencies_groups[$group_key] = [
						'id' => count($dependencies_groups)+1,
						'name' => 'Groupe non redondé',
						'redundant' => 0,
						'groupstate' => $row['newStateForChild'],
						'idservice' => $row['idService'],
						'idmessage' => $idmessage+1,
					];
				}

				$key = $dependencies_groups[$group_key]['id'].'-'.$row['idServiceParent'].'-'.$row['stateOfParent'];
				$dependencies_groups_content[$key] = [
					'idgroup' => $dependencies_groups[$group_key]['id'],
					'idservice' => $row['idServiceParent'],
					'servicestate' => $row['stateOfParent'],
				];
			}

			// Migrate "dependencies_groups" data.
			echo ' ==   - Migrate "dependencies_groups" data.'.PHP_EOL;
			$table = $this->table('dependencies_groups');
			foreach ($dependencies_groups as $data){
				$table->insert($data);
			}
			$table->saveData();

			// Migrate "dependencies_groups_content" data.
			echo ' ==   - Migrate "dependencies_groups_content" data.'.PHP_EOL;
			$table = $this->table('dependencies_groups_content', array('id' => false, 'primary_key' => array('idgroup', 'idservice', 'servicestate')));
			foreach ($dependencies_groups_content as $data){
				$table->insert($data);
			}
			$table->saveData();

			// Migrate "dependencies_messages" data.
			echo ' ==   - Migrate "dependencies_messages" data.'.PHP_EOL;
			$table = $this->table('dependencies_messages');
			foreach ($dependencies_messages as $data){
				$table->insert($data);
			}
			$table->saveData();

			// Drop old "dependencies" table.
			echo ' ==   - Drop old "dependencies" table.'.PHP_EOL;
			$this->dropTable('dependencies');
		}


		// Events table.
		if ($this->hasTable('events_old')) {
			echo ' ==  Events table...'.PHP_EOL;

			// Migrate "events" data.
			echo ' ==   - Migrate "events" data.'.PHP_EOL;
			$table = $this->table('events');
			$rows = $this->query('SELECT * FROM events_old eo, events_isou_old eio WHERE eo.idEvent=eio.idEvent');
			foreach ($rows as $row) {
				// TypeEvent values : 0 = Isou, 1 = Nagios, 2 = Message.
				// IsScheduled values : 0 = unscheduled events, 1 = scheduled, 2 = regular, 3 = closed.
				$period = null;
				switch ($row['isScheduled']) {
					case '0':
						$state = 1;
						$type = 0;
						break;
					case '1':
						$state = 1;
						$type = 1;
						break;
					case '2':
						$period = $row['period'];
						$state = 4;
						$type = 1;
						break;
					case '3':
						$state = 4;
						$type = 1;
						break;
				}

				$data = [
					// 'id' => $row['idEvent'],
					'begindate' => $row['beginDate'],
					'enddate' => $row['endDate'],
					'state' => $state,
					'type' => $type,
					'period' => $period,
					'ideventdescription' => $row['idEventDescription'],
					'idservice' => $row['idService'],
				];
				$table->insert($data);
			}

			$rows = $this->query('SELECT * FROM events_old eo, events_nagios_old eno WHERE eo.idEvent=eno.idEvent');
			foreach ($rows as $row) {
				$data = [
					// 'id' => $row['idEvent'],
					'begindate' => $row['beginDate'],
					'enddate' => $row['endDate'],
					'state' => $row['state'],
					'type' => 0,
					'period' => null,
					'ideventdescription' => 0,
					'idservice' => $row['idService'],
				];
				$table->insert($data);
			}
			$table->saveData();

			// Drop old "events_old" table.
			echo ' ==   - Drop old "events_old" table.'.PHP_EOL;
			$this->dropTable('events_old');

			// Drop old "events_isou_old" table.
			echo ' ==   - Drop old "events_isou_old" table.'.PHP_EOL;
			$this->dropTable('events_isou_old');

			// Drop old "events_nagios_old" table.
			echo ' ==   - Drop old "events_nagios_old" table.'.PHP_EOL;
			$this->dropTable('events_nagios_old');
		}


		// Events_descriptions table.
		if ($this->hasTable('events_description_old')) {
			echo ' ==  Events_descriptions table...'.PHP_EOL;

			// Migrate "events_descriptions" data.
			echo ' ==   - Migrate "events_descriptions" data.'.PHP_EOL;
			$table = $this->table('events_descriptions');
			$rows = $this->query('SELECT * FROM events_description_old');
			foreach ($rows as $row) {
				$data = [
					'id' => $row['idEventDescription'],
					'description' => $row['description'],
					'autogen' => $row['autogen'],
				];
				$table->insert($data);
			}
			$table->saveData();

			// Drop old "events_description_old" table.
			echo ' ==   - Drop old "events_description_old" table.'.PHP_EOL;
			$this->dropTable('events_description_old');
		}


		// Services table.
		if ($this->hasTable('services')) {
			echo ' ==  Services table...'.PHP_EOL;

			// Migrate "services" data.
			echo ' ==   - Migrate "services" data.'.PHP_EOL;
			$table = $this->table('services');
			$rows = $this->query('SELECT * FROM services_old');
			foreach ($rows as $row) {
				if (empty($row['nameForUsers'])) {
					$idtype = 2; // Type Nagios.
				} else {
					$idtype = 1; // Type Isou.
					$row['name'] = $row['nameForUsers'];
				}

				if (empty($row['url'])) {
					$row['url'] = null;
				}

				if (empty($row['comment'])) {
					$row['comment'] = null;
				}

				$data = [
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
					'idcategory' => $row['idCategory']
				];
				$table->insert($data);
			}
			$table->saveData();

			// Drop old "services_old" table.
			echo ' ==   - Drop old "services_old" table.'.PHP_EOL;
			$this->dropTable('services_old');
		}


		// Events_info table.
		if ($this->hasTable('events_info')) {
			echo ' ==  Events_info table...'.PHP_EOL;

			// Drop old "events_info" table.
			echo ' ==   - Drop old "events_info" table.'.PHP_EOL;
			$this->dropTable('events_info');
		}

		// Statistics table.
		if ($this->hasTable('statistics')) {
			echo ' ==  Statistics table...'.PHP_EOL;

			// Drop old "statistics" table.
			echo ' ==   - Drop old "statistics" table.'.PHP_EOL;
			$this->dropTable('statistics');
		}
	}

	/**
	* Migrate Down.
	*/
	public function down()
	{

	}
}
