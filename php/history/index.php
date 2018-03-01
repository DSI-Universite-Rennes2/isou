<?php

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/classes/helpers/simple_menu.php';

$TITLE .= ' - Historique des évènements';

if(isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])){
	$page = $PAGE_NAME[2];
}else{
	$page = 1;
}

if(isset($PAGE_NAME[4])){
	$options_filter = explode(';', $PAGE_NAME[4]);
	if(count($options_filter) === 6){
		$_POST['services'] = explode(',', $options_filter[0]);
		$_POST['event_type'] = $options_filter[1];
		$_POST['year'] = $options_filter[2];
		$_POST['month'] = $options_filter[3];
		$_POST['sort'] = $options_filter[4];
		$_POST['paging'] = $options_filter[5];
	}
}

// services
$options_services = get_isou_services_sorted_by_idtype();
$smarty->assign('options_services', $options_services);
$options_event_types = array(
	-1 => 'Tous',
	UniversiteRennes2\Isou\Event::TYPE_SCHEDULED => 'Prévues',
	UniversiteRennes2\Isou\Event::TYPE_UNSCHEDULED => 'Non prévues'
);
$smarty->assign('options_event_types', $options_event_types);

// years
$current_year = date('Y');
$options_years = array($current_year => $current_year);
$databases_directory = dirname(substr(DB_PATH, 7));
if(is_dir($databases_directory)){
	if($handle = opendir($databases_directory)){
		while(($file = readdir($handle)) !== FALSE){
			if(preg_match('#isou-(\d{4})\.sqlite3#', $file, $year) > 0){
				$options_years[$year[1]] = $year[1];
			}
		}
		closedir($handle);
	}
}
krsort($options_years);
$options_years = array(-1 => 'Toutes les années') + $options_years;
$smarty->assign('options_years', $options_years);

// months
$options_months = array(-1 => 'Tous les mois');
for($i=1;$i<13;$i++){
	$options_months[$i] = strftime('%B', mktime(0, 0, 0, $i));
}
$smarty->assign('options_months', $options_months);

// sort
$options_sorts = array('Décroissant', 'Croissant');
$smarty->assign('options_sorts', $options_sorts);

// max result
$options_paging = array('-1' => 'illimité');
for($i=10;$i<101;$i=$i+10){
	$options_paging[$i] = $i;
}
$smarty->assign('options_paging', $options_paging);

if(isset($_POST['services'], $_POST['event_type'], $_POST['year'], $_POST['month'], $_POST['sort'], $_POST['paging'])){
	$events = array();

	$params = array();

	// services
	$sql_services = array();
	if(is_array($_POST['services'])){
		foreach($_POST['services'] as $service){
			if(ctype_digit($service)){
				$sql_services[] = $service;
			}
		}
	}else{
		$_POST['services'] = array();
	}

	if(isset($sql_services[0])){
		$params = $sql_services;
		$sql_services = " AND s.id IN(?".str_repeat(',?', count($params)-1).")";
	}else{
		$sql_services = '';
	}

	// event type
	if(!isset($options_event_types[$_POST['event_type']])){
		$_POST['event_type'] = '-1';
	}

	if($_POST['event_type'] === '-1'){
		$sql_events = " AND e.type < 2";
	}else{
		$sql_events = " AND e.type = ?";
		$params[] = $_POST['event_type'];
	}

	// year
	$databases = array();
	if(isset($options_years[$_POST['year']])){
		if($_POST['year'] === '-1'){
			$databases = $options_years;
			unset($databases[-1]);
		}else{
			$databases = array($_POST['year'] => $_POST['year']);
		}

		foreach($databases as $index => $filename){
			if($index == $current_year){
				$databases[$index] = DB_PATH;
			}else{
				$databases[$index] = str_replace('isou.sqlite3', 'isou-'.$index.'.sqlite3', DB_PATH);
			}
		}
	}else{
		$databases[] = DB_PATH;
	}

	// month
	if($_POST['month'] !== '-1' && isset($options_months[$_POST['month']])){
		$sql_months = " AND e.begindate LIKE '____-".$_POST['month']."-__%'";
	}else{
		$sql_months = '';
	}

	// sort
	if($_POST['sort'] === '0'){
		$sql_sort = " ORDER BY e.begindate DESC";
		krsort($databases);
	}else{
		$sql_sort = " ORDER BY e.begindate ASC";
		ksort($databases);
	}

	// paging
	if($_POST['paging'] !== '-1'){
		if(!isset($options_paging[$_POST['paging']])){
			$_POST['paging'] = '10';
		}

		$sql_limit = " LIMIT ".$_POST['paging']." OFFSET ".(($page-1)*$_POST['paging']);
	}else{
		$sql_limit = '';
	}


	$events = array();
	$sql = "SELECT s.name, e.begindate, e.enddate, ed.description, e.type".
		" FROM events e, events_descriptions ed, services s".
		" WHERE s.id = e.idservice".
		" AND ed.id = e.ideventdescription".
		$sql_services.
		$sql_events.
		$sql_months.
		" AND s.idtype=?".
		$sql_sort;

	if(!isset($_POST['export'])){
		$sql .= $sql_limit;

		$count_events = 0;
		$sql_count = "SELECT COUNT(e.id)".
			" FROM events e, events_descriptions ed, services s".
			" WHERE s.id = e.idservice".
			" AND ed.id = e.ideventdescription".
			$sql_services.
			$sql_events.
			$sql_months.
			" AND s.idtype=?";
	}

	$params[] = UniversiteRennes2\Isou\Service::TYPE_ISOU;

	foreach($databases as $database){
		if($database === DB_PATH){
			$db = $DB;
		}else{
			try{
				if(is_file(substr($database, 7))){
					$db = new PDO($database);
				}else{
					$db = null;
				}
			}catch(PDOException $e){
				$db = null;
			}
		}

		if($db === NULL){
			continue;
		}

		$query = $db->prepare($sql);
		$query->execute($params);
		foreach($query->fetchAll(PDO::FETCH_OBJ) as $event){
			try{
				$event->begindate = new DateTime($event->begindate);
				if($event->enddate !== NULL){
					$event->enddate = new DateTime($event->enddate);
					$diff = $event->begindate->diff($event->enddate);
					$event->total_minutes = round(($event->enddate->getTimestamp()-$event->begindate->getTimestamp())/60);
				}else{
					$diff = $event->begindate->diff(new DateTime());
					$event->total_minutes = round((TIME-$event->begindate->getTimestamp())/60);
				}

				list($days, $hours, $minutes) = explode(';', $diff->format('%a;%h;%i'));

				$event->total = array();

				if($days === '1'){
					$event->total[] = '1 jour';
				}elseif($days > 1){
					$event->total[] = $days.' jours';
				}

				if($hours === '1'){
					$event->total[] = '1 heure';
				}elseif($hours > 1){
					$event->total[] = $hours.' heures';
				}

				if($minutes > 1){
					$event->total[] = $minutes.' minutes';
				}else{
					$event->total[] = $minutes.' minute';
				}

				$event->total = implode(', ', $event->total);
			}catch(Exception $exception){
				$LOGGER->addError($exception->getMessage());
				continue;
			}

			$events[] = $event;
		}

		if(!isset($_POST['export'])){
			$query = $db->prepare($sql_count);
			$query->execute($params);
			$count = $query->fetch(PDO::FETCH_NUM);
			$count_events += $count[0];
		}

		$db = NULL;
	}

	$smarty->assign('events', $events);

	if(isset($_POST['export'])){
		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=isou_export.csv');
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Transfer-Encoding: binary');
		$smarty->display('history/export.tpl');
		exit(0);
	}else{
		$smarty->assign('count_events', $count_events);
	}

	// pagination
	if($_POST['paging'] === '-1'){
		$count_pages = 1;
	}else{
		$count_pages = ceil($count_events/$_POST['paging']);
	}

	$options_filter = array();
	$options_filter[] = implode(',', $_POST['services']);
	$options_filter[] = $_POST['event_type'];
	$options_filter[] = $_POST['year'];
	$options_filter[] = $_POST['month'];
	$options_filter[] = $_POST['sort'];
	$options_filter[] = $_POST['paging'];
	$options_filter = implode(';', $options_filter);

	$pagination = array();
	for($i=1;$i<=$count_pages;$i++){
		$selected = ($page == $i);
		$url = URL.'/index.php/statistiques/page/'.$i.'/filter/'.$options_filter.'#resultat';
		$pagination[] = new Isou\Helpers\SimpleMenu($i, 'Page '.$i, $url, $selected);
	}

	$smarty->assign('pagination', $pagination);
}

$TEMPLATE = 'history/index.tpl';

?>
