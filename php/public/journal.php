<?php

use UniversiteRennes2\Isou\Plugin;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';

$TITLE .= ' - Journal';

foreach (get_services(array('plugin' => PLUGIN_ISOU, 'visible' => true)) as $service) {
	$services[$service->id] = $service->name;
}

$plugin = Plugin::get_plugin(array('codename' => 'isou'));

$options = array();
$options['tolerance'] = $plugin->settings->tolerance;
$options['plugin'] = PLUGIN_ISOU;

$days = array();
for($i=0;$i<7;$i++){
	$days[$i] = new stdClass();
	$days[$i]->date = new DateTime(strftime('%Y-%m-%d 00:00:00', TIME-($i*24*60*60)));

	$options['since'] = $days[$i]->date;
	$options['before'] = clone $days[$i]->date;
	$options['before']->setTime(23, 59, 59);
	$days[$i]->events = get_events($options);
	foreach($days[$i]->events as $j => $event){
		if($event->enddate === NULL && $event->startdate->format('Y-m-d') > $days[$i]->date->format('Y-m-d')){
			unset($days[$i]->events[$j]);
		}else if (isset($services[$event->idservice]) === false) {
			unset($days[$i]->events[$j]);
		} else {
			$event->service = $services[$event->idservice];
		}
	}
}

$smarty->assign('days', $days);

$TEMPLATE = 'public/journal.tpl';
