<?php

$submenu = array();
$submenu['prevus'] = new Isou\Helpers\SimpleMenu('interruptions prévues', '', URL.'/index.php/evenements/prevus');
$submenu['imprevus'] = new Isou\Helpers\SimpleMenu('interruptions non prévues', '', URL.'/index.php/evenements/imprevus');

switch($PAGE_NAME[1]){
	case 'imprevus':
		$submenu['imprevus']->selected = TRUE;
		$since = strftime('%Y-%m-%dT%H:%M', mktime(0,0,0)-35*24*60*60);
		$events = get_events_by_type(NULL, UniversiteRennes2\Isou\Event::TYPE_UNSCHEDULED, UniversiteRennes2\Isou\Service::TYPE_ISOU);
		break;
	case 'prevus':
	default:
		$submenu['prevus']->selected = TRUE;
		$since = strftime('%Y-%m-%dT%H:%M', mktime(0,0,0)-35*24*60*60);
		$events = get_events_by_type($since, UniversiteRennes2\Isou\Event::TYPE_SCHEDULED, UniversiteRennes2\Isou\Service::TYPE_ISOU);
}

$smarty->assign('events', $events);
$smarty->assign('submenu', $submenu);

$TEMPLATE = 'events/list.tpl';

?>
