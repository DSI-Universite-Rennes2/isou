<?php

header('content-type: application/xml');

define('MAXFEED', 100);

require __DIR__.'/../config.php';

// find filter on rss url
if(isset($_GET['key'])){
	$maxKey = pow(2,100);
	$key = hexdec($_GET['key']);
	$keys = array();
	$i=100;
	while($key != 0){
		if($key >= $maxKey){
			$keys[$i] = $i;
			$key = $key-$maxKey;
		}
		$maxKey /= 2;
		$i--;
	}
}else{
	$keys = null;
}

$now = mktime(0,0,0);
$record = array();
$items = array();

try{
	if(!is_file(substr(DB_PATH, 7))){
		throw new PDOException(DB_PATH.' n\'existe pas.');
	}
	$DB = new PDO(DB_PATH, '', '');
}catch(PDOException $e){
	header("HTTP/1.0 503 Service Unavailable");

	// close pdo connection
	$DB = null;

	exit(0);
}

$smarty = new Smarty();
$smarty->setTemplateDir(PRIVATE_PATH.'/html/');
$smarty->setCompileDir(PRIVATE_PATH.'/cache/smarty/');

// Charge la configuration.
require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

// Charge les plugins.
$plugins = get_plugins();

require PRIVATE_PATH.'/libs/events.php';
require PRIVATE_PATH.'/libs/services.php';

$services = get_services_sorted_by_id(PLUGIN_ISOU);

$items = array();
$options = array();
$options['since'] = new DateTime(strftime('%Y-%m-%d', TIME-(30*24*60*60)));

foreach(get_events($options) as $event){
	if(!isset($services[$event->idservice])){
		continue;
	}

	if($keys !== NULL && !isset($keys[$event->idservice])){
		continue;
	}

	$opening_event_id = $event->startdate->format('Y-m-d\TH:i:s').'_'.$event->id;
	$items[$opening_event_id] = clone $event;
	if($event->type === UniversiteRennes2\Isou\Event::TYPE_SCHEDULED){
		$items[$opening_event_id]->title = 'Interruption : '.$services[$event->idservice];
	}else{
		$items[$opening_event_id]->title = 'Interruption non prévue : '.$services[$event->idservice];
	}
	$items[$opening_event_id]->link = URL.'#'.$opening_event_id;
	$items[$opening_event_id]->guid = $opening_event_id;
	$items[$opening_event_id]->pubdate = gmdate('D, d M Y H:i:s', $event->startdate->getTimestamp());

	if($event->enddate === NULL){
		$enddate = 'indéterminé';
	}else{
		$enddate = $event->enddate->format('d/m/Y H:i');
	}

	if(empty($items[$opening_event_id]->description)){
		$description = 'n/a';
	}else{
		$description = $items[$opening_event_id]->description;
	}

	$items[$opening_event_id]->description = 'Date de début : '.$event->startdate->format('d/m/Y H:i').'<br />'.
		'Date de fin : '.$enddate.'<br /><br />'.
		'Description : '.$description;

	if($event->enddate !== NULL){
		$ending_event_id = $event->enddate->format('Y-m-d\TH:i:s').'_'.$event->id;
		$items[$ending_event_id] = clone $event;
		$items[$ending_event_id]->title = 'Remise en route : '.$services[$event->idservice];
		$items[$ending_event_id]->link = URL.'#'.$ending_event_id;
		$items[$ending_event_id]->guid = $ending_event_id;
		$items[$ending_event_id]->pubdate = gmdate('D, d M Y H:i:s', $event->enddate->getTimestamp());

		$items[$ending_event_id]->description = 'Date de début : '.$event->startdate->format('d/m/Y H:i').'<br />'.
			'Date de fin : '.$event->enddate->format('d/m/Y H:i').'<br /><br />'.
			'Description : '.$description;
	}
}

krsort($items);

if(count($items) > MAXFEED){
	$items = array_slice($items, 0, MAXFEED);
}

$smarty->assign('site_header', $CFG['site_header']);
$smarty->assign('items', $items);
$smarty->assign('last_build_date', gmdate('D, d M Y H:i:s', TIME));

$smarty->display('public/rss.tpl');
