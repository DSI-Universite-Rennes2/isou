<?php

$TITLE = NAME.' - Calendrier';

$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_calendar.js');

require PRIVATE_PATH.'/classes/isou/isou_service.class.php';
require PRIVATE_PATH.'/classes/isou/isou_event.class.php';

$date = getdate();
if(isset($_GET['p'])){
	$page = intval($_GET['p']);
}else{
	$page = 0;
}

// $CALENDAR_STEP = 'WEEKLY';
$CALENDAR_STEP = 'MONTHLY';

if($CALENDAR_STEP === 'WEEKLY'){
	$time = mktime(0,0,0)-((6+$date["wday"]-($page*7)))*24*60*60;
}else{
	$first_months_day = mktime(0,0,0,$date['mon']+$page,1);
	$time = $first_months_day-((intval(strftime('%u', $first_months_day))-1)*24*60*60);
}

$beginDate = strftime('%Y-%m-%dT%H:%M', $time);
$endDate = strftime('%Y-%m-%dT%H:%M', $time+35*24*60*60);

// recupere tous les services dans la bdd
$sql = "SELECT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, C.name AS category".
		" FROM services S, categories C".
		" WHERE S.idCategory = C.idCategory".
		" AND S.nameForUsers IS NOT NULL".
		" AND S.enable = 1".
		" AND S.visible = 1".
		" ORDER BY C.position, UPPER(S.nameForUsers)";

$i=0;
$services = array();
if($service_records = $DB->query($sql)){
	while($service = $service_records->fetchObject('IsouService')){
		$service->setEvents($service->getScheduledEvents($CFG['tolerance'], -1, $beginDate, $endDate));
		if($service->hasEvents() === TRUE){
			$services[$i] = $service;
			$i++;
		}
	}
}

if($page === 1){
	$smarty->assign('previousWeekLink', '');
}else{
	if($IS_ADMIN){
		$smarty->assign('previousWeekLink', '?p='.($page-1));
	}elseif($page>1){
		$smarty->assign('previousWeekLink', '?p='.($page-1));
	}
}
$smarty->assign('nextWeekLink', '?p='.($page+1));

$array_events = IsouEvent::$array_events;

$calendar = array();
$rows = array();

for($row=0;$row<5;$row++){
	$cols = array();
	for($col=0;$col<7;$col++){
		$day = new stdClass();
		$day->time = $time;

		if($row === 0){
			if(strftime('%d', $time) === '01'){
				if(strftime('%m', $time) === '01'){
					$day->strftime = '1er %B %Y';
				}else{
					$day->strftime = '1er %B';
				}
			}else{
				$day->strftime = '%d %B';
			}
		}else{
			$day->strftime = '%d';
		}

		$events = array();

		if(isset($array_events[strftime('%Y-%m-%d',$time)]) && is_array($array_events[strftime('%Y-%m-%d',$time)])){
			$i=0;
			while(isset($array_events[strftime('%Y-%m-%d',$time)][$i])){
				$nameForUsers = $array_events[strftime('%Y-%m-%d',$time)][$i];
				$event = new stdClass();
				$event->stripName = strip_accents(substr($nameForUsers,0,-3));
				$event->name = substr($nameForUsers,0,-3);
				$events[] = $event;
				$i++;
			}
		}
		if(count($events) > 0){
			$day->events = $events;
		}
		
		$cols[] = $day;
		$time = $time+(24*60*60);
	}
	$rows[] = $cols;
}

$smarty->assign('calendar', $rows);
$smarty->assign('now', mktime(0,0,0));

require PRIVATE_PATH.'/php/public/news.php';

$template = 'public/calendar.tpl';

?>

