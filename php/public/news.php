<?php

use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$TITLE .= ' - Actualité';

$services = array();
$categories = array();

$idcategories = get_categories_sorted_by_id();

$plugin = Plugin::get_plugin(array('codename' => 'isou'));

$options = array();
$options['tolerance'] = $plugin->settings->tolerance;
$options['plugin'] = PLUGIN_ISOU;
$options['since'] = new DateTime();
$options['since']->sub(new DateInterval('P2D')); // TODO: create CFG variable

$events = get_events($options);
foreach($events as $event){
	if(isset($services[$event->idservice])){
		$service = $services[$event->idservice];
	}else{
		$service = get_service(array('id' => $event->idservice, 'visible' => true, 'plugin' => PLUGIN_ISOU));
		if($service === FALSE){
			continue;
		}else{
			$services[$event->idservice] = $service;
		}
	}

	// on ne garde que les services ISOU non fermés
	if($service->state === State::CLOSED){
		continue;
	}

	if(!isset($categories[$service->idcategory]->services[$event->idservice])){
		// initialise la categorie
		if(!isset($categories[$service->idcategory])){
			$categories[$service->idcategory] = new stdClass();
			$categories[$service->idcategory]->name = $idcategories[$service->idcategory];
			$categories[$service->idcategory]->services = array();
		}

		// ajoute le service à la catégorie
		$categories[$service->idcategory]->services[$event->idservice] = $service;
		$categories[$service->idcategory]->services[$event->idservice]->events = array();
	}

	// ajoute l'évènement au service
	$categories[$service->idcategory]->services[$event->idservice]->events[] = $event;
}

$smarty->assign('categories', $categories);

$TEMPLATE = 'public/news.tpl';
