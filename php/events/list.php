<?php

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;
use Isou\Helpers\SimpleMenu;

$submenu = array();
$submenu['prevus'] = new SimpleMenu('interruptions prévues', '', URL.'/index.php/evenements/prevus');
$submenu['imprevus'] = new SimpleMenu('interruptions non prévues', '', URL.'/index.php/evenements/imprevus');
$submenu['reguliers'] = new SimpleMenu('interruptions régulières', '', URL.'/index.php/evenements/reguliers');
$submenu['fermes'] = new SimpleMenu('service fermé', '', URL.'/index.php/evenements/fermes');

if ($PAGE_NAME[1] === 'fermes') {
	$submenu['fermes']->selected = TRUE;

	$events = array();
	$services = get_services(array('locked' => true));
	foreach($services as $service) {
		$events[] = $service->get_current_event();
	}
} else {
	$options = array();
	$options['since'] = strftime('%Y-%m-%dT%H:%M', mktime(0,0,0)-35*24*60*60);
	$options['service_type'] = Service::TYPE_ISOU;

	switch($PAGE_NAME[1]){
		case 'imprevus':
			$submenu['imprevus']->selected = TRUE;
			$options['type'] = Event::TYPE_UNSCHEDULED;
			break;
		case 'reguliers':
			$submenu['reguliers']->selected = TRUE;
			$options['type'] = Event::TYPE_REGULAR;
			break;
		case 'prevus':
		default:
			$submenu['prevus']->selected = TRUE;
			$options['type'] = Event::TYPE_SCHEDULED;

	}

	$events = get_events($options);
}

$smarty->assign('events', $events);
$smarty->assign('submenu', $submenu);
$smarty->assign('services', get_services_sorted_by_id(Service::TYPE_ISOU));

$TEMPLATE = 'events/list.tpl';
