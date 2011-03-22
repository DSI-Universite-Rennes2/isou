<?php
$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/calendar.css" media="screen" />'.
		'<link rel="stylesheet" type="text/css" href="'.URL.'/css/news.css" media="screen" />';
$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="'.URL.'/js/jquery_calendar.js"></script>';
$title = NAME.' - Calendrier';

require BASE.'/classes/isou/isou_service.class.php';
require BASE.'/classes/isou/isou_event.class.php';

$date = getdate();
if(isset($_GET['p'])){
	$page = intval($_GET['p']);
}else{
	$page = 0;
}

define('TIMESTAMP_OF_FIRST_CALENDAR_DAY',mktime(0,0,0)-((6+$date["wday"]-($page*7)))*24*60*60);
define('TIMESTAMP_OF_MONDAY_OF_LAST_WEEK',mktime(0,0,0)-(6+$date["wday"])*24*60*60);
define('TIMESTAMP_OF_LAST_CALENDAR_DAY',((mktime(0,0,0)-((6+$date["wday"]-($page*7)))*24*60*60)+(35*24*60*60)));

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
if($service_records = $db->query($sql)){
	while($service = $service_records->fetchObject('IsouService')){
		if($_SESSION['hide'] === 1 || $IS_ADMIN === FALSE){
			$tolerance = TOLERANCE;
		}else{
			$tolerance = 0;
		}
		$service->setEvents($service->getScheduledEvents($tolerance, -1, TIMESTAMP_OF_FIRST_CALENDAR_DAY, TIMESTAMP_OF_LAST_CALENDAR_DAY));
		if($service->hasEvents() === TRUE){
			$services[$i] = $service;
			$i++;
		}
	}
}

if($page>0){
	$tabindex++;
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

$time = mktime(0,0,0)-(24*(6+$date["wday"]-($page*7)))*60*60;
$array_events = IsouEvent::$array_events;

$calendar = array();
$rows = array();

for($row=0;$row<5;$row++){
	$cols = array();
	for($col=0;$col<7;$col++){
		$day = new stdClass();

		$labelday = date('j', $time);
		if($labelday == 1 || $row == 0){
			if($labelday == 1 && date('n',$time) == 1){
				$labelday = strftime("1er %B %Y",$time);
			}else if($labelday == 1){
				$labelday = strftime("1er %B",$time);
			}else{
				$labelday = strftime("%d %B",$time);
			}
		}
		$d = $row*7+$col;

		if($d < (6+$date["wday"]-$page*7)){
			$events = array();

			if(isset($array_events[strftime('%m/%d/%y',$time)]) && is_array($array_events[strftime('%m/%d/%y',$time)])){
				$i=0;
				while(isset($array_events[strftime('%m/%d/%y',$time)][$i])){
					$nameForUsers = $array_events[strftime('%m/%d/%y',$time)][$i];
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

			$day->cssClass = 'past';
			$day->dateId = strftime('%d-%B-%Y',$time);
			$day->date = $labelday;

		}else if($d === (6+$date["wday"]-$page*7)){
			$events = array();

			if(isset($array_events[strftime('%m/%d/%y',$time)])){
				$i=0;
				while(isset($array_events[strftime('%m/%d/%y',$time)][$i])){
					$nameForUsers = $array_events[strftime('%m/%d/%y',$time)][$i];
					$event = new stdClass();
					if(substr($nameForUsers,-1)>0){
						$event->stripName = strip_accents(substr($nameForUsers,0,-3));
						$event->name = substr($nameForUsers,0,-3);
					}else{
						$event->stripName = strip_accents(substr($nameForUsers,0,-3));
						$event->name = substr($nameForUsers,0,-3);
					}
					$events[] = $event;
					$i++;
				}
			}

			if(count($events) > 0){
				$day->events = $events;
			}

			$day->cssClass = 'today';
			$day->dateId = strftime('%d-%B-%Y',$time);
			$day->date = $labelday;
		}else{
			$events = array();
			if(isset($array_events[strftime('%m/%d/%y',$time)]) && is_array($array_events[strftime('%m/%d/%y',$time)])){
				$i=0;
				while(isset($array_events[strftime('%m/%d/%y',$time)][$i])){
					$nameForUsers = $array_events[strftime('%m/%d/%y',$time)][$i];
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

			$day->dateId = strftime('%d-%B-%Y',$time);
			$day->date = $labelday;
		}
		$cols[] = $day;
		$time = $time+(24*60*60);
	}
	$rows[] = $cols;
}

$smarty->assign('calendar', $rows);

require BASE.'/php/public_news.php';

?>

