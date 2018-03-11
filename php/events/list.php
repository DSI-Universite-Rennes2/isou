<?php

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;
use Isou\Helpers\SimpleMenu;

$submenu = array();
$submenu['prevus'] = new SimpleMenu('interruptions prévues', '', URL.'/index.php/evenements/prevus');
$submenu['imprevus'] = new SimpleMenu('interruptions non prévues', '', URL.'/index.php/evenements/imprevus');
$submenu['reguliers'] = new SimpleMenu('interruptions régulières', '', URL.'/index.php/evenements/reguliers');
$submenu['fermes'] = new SimpleMenu('service fermé', '', URL.'/index.php/evenements/fermes');

if ($PAGE_NAME[1] === 'fermes') {
	$submenu['fermes']->selected = TRUE;

	$events = array();
	$services = get_services(array('state' => State::CLOSED));
	foreach($services as $service) {
		$events[] = $service->get_current_event();
	}

	$subtemplate = 'events/list_closed_events.tpl';
} else {
	$options = array();
	$options['service_type'] = Service::TYPE_ISOU;
	$options['sort'] = array('e.enddate IS NULL DESC', 'e.enddate DESC', 'e.begindate DESC');

	switch($PAGE_NAME[1]){
		case 'imprevus':
			$submenu['imprevus']->selected = TRUE;
			$options['since'] = strftime('%FT%T', mktime(0,0,0)-35*24*60*60);
			$options['type'] = Event::TYPE_UNSCHEDULED;
			$subtemplate = 'events/list_default_events.tpl';
			break;
		case 'reguliers':
			$submenu['reguliers']->selected = TRUE;
			$options['type'] = Event::TYPE_REGULAR;
			$subtemplate = 'events/list_regular_events.tpl';
			break;
		case 'prevus':
		default:
			$submenu['prevus']->selected = TRUE;
			$options['since'] = strftime('%FT%T', mktime(0,0,0)-35*24*60*60);
			$options['type'] = Event::TYPE_SCHEDULED;
			$subtemplate = 'events/list_default_events.tpl';
	}

	$events = get_events($options);
}

$smarty->assign('events', $events);
$smarty->assign('submenu', $submenu);
$smarty->assign('subtemplate', $subtemplate);
$smarty->assign('services', get_services_sorted_by_id(Service::TYPE_ISOU));

$TEMPLATE = 'events/list.tpl';
