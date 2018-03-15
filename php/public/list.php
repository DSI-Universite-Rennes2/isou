<?php

use UniversiteRennes2\Isou\Service;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$TITLE .= ' - Liste';

$since = new DateTime();
$since->sub(new DateInterval('P2D')); // TODO: create CFG variable

$categories = array();
foreach (get_categories() as $category) {
	$categories[$category->id] = $category;
	$categories[$category->id]->services = array();
}

$services = get_services(array('plugin' => PLUGIN_ISOU, 'visible' => true));

foreach($services as $service){
	if($service->enable === '0' || $service->visible === '0'){
		continue;
	}

	// changement de categorie
	if (isset($categories[$service->idcategory]) === false) {
		continue;
	}

	// ajout des évènements
	if($service->is_closed === TRUE){
		$service->closed_event = $service->get_closed_event();
	}else{
		$service->last_event = $service->get_last_events(array('since' => $since, 'one_record' => TRUE));
		$service->next_scheduled_event = $service->get_next_scheduled_events(array('one_record' => TRUE));
		$service->regular_events = $service->get_regular_events();
	}

	$categories[$service->idcategory]->services[] = $service;
}

$smarty->assign('categories', $categories);

$TEMPLATE = 'public/list.tpl';
