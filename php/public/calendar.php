<?php

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';

$TITLE .= ' - Calendrier';

$_GET['page'] = 1;
if(isset($PAGE_NAME[1]) && ctype_digit($PAGE_NAME[1])){
	if($PAGE_NAME[1] === '0'){
		$_GET['page'] = 1;
	}elseif($PAGE_NAME[1] > 5){
		$_GET['page'] = 5;
	}else{
		$_GET['page'] = $PAGE_NAME[1];
	}
}

$date = getdate();

// $CALENDAR_STEP = 'WEEKLY'; // TODO: faire une option
$CALENDAR_STEP = 'MONTHLY';

if($CALENDAR_STEP === 'WEEKLY'){
	$time = mktime(0,0,0)-((6+$date['wday']-(($_GET['page']-1)*7)))*24*60*60;
}else{
	$first_months_day = mktime(0,0,0,$date['mon']+$_GET['page']-1,1);
	$time = $first_months_day-((intval(strftime('%u', $first_months_day))-1)*24*60*60);
}

$begincalendar = strftime('%Y-%m-%dT%H:%M', $time);
$endcalendar = strftime('%Y-%m-%dT%H:%M', $time+35*24*60*60);

$one_day = new DateInterval('P1D');
$calendar = array();
$calendar_day = new stdClass();
$calendar_day->datetime = $time;
$calendar_day->services = array();
for($i=0;$i<5;$i++){
	for($j=0;$j<7;$j++){
		if($i === 0){
			if(strftime('%d', $calendar_day->datetime) === '01'){
				if(strftime('%m', $calendar_day->datetime) === '01'){
					$calendar_day->strftime = '1er %B %Y';
				}else{
					$calendar_day->strftime = '1er %B';
				}
			}else{
				$calendar_day->strftime = '%d %B';
			}
		}else{
			$calendar_day->strftime = '%d';
		}

		$calendar[$i][$j] = clone($calendar_day);
		$calendar_day->datetime += 24*60*60;
	}
}

$begincalendar = new DateTime($begincalendar);
$endcalendar = new DateTime($endcalendar);

$options = array();
$options['tolerance'] = $CFG['tolerance'];
$options['service_type'] = Service::TYPE_ISOU;
$options['type'] = Event::TYPE_SCHEDULED;
$options['since'] = $begincalendar;

$events = get_events($options);
foreach($events as $i => $event){
	$service = get_service(array('id' => $event->idservice, 'visible' => true));

	if ($service === false) {
		unset($events[$i]);
		continue;
	}

	if($event->state === State::CLOSED){
		unset($events[$i]);
		continue;
	}

	if($event->enddate === NULL){
		if($_GET['page'] != 1){
			unset($events[$i]);
			continue;
		}
	}elseif(!($event->begindate >= $begincalendar && $event->enddate <= $endcalendar)){
		unset($events[$i]);
		continue;
	}

	$event->service = $service->name;
	$service->idevent = $event->id;

	$begindate = clone $event->begindate;
	$begindate->setTime(0,0,0);
	$interval = $begincalendar->diff($begindate);

	if($interval->invert === 1){
		$begindate = clone $begincalendar;
		$i = 0;
		$j = 0;
	}else{
		$i = round($interval->d/5, 0)-1;
		if($i < 0){
			$i = 0;
		}
		$j = $interval->d%7;
	}

	if($event->enddate === NULL){
		$enddate = new DateTime();
	}else{
		$enddate = clone $event->enddate;
	}

	while($begindate < $enddate){
		if(!isset($calendar[$i][$j])){
			break;
		}

		$calendar[$i][$j]->services[] = $service;
		$begindate->add($one_day);

		$j++;
		if($j > 6){
			$j=0;
			$i++;
		}
	}
}

$smarty->assign('calendar', $calendar);
$smarty->assign('events', $events);
$smarty->assign('now', mktime(0,0,0));

$TEMPLATE = 'public/calendar.tpl';
