<?php

use UniversiteRennes2\Isou\Service;

$TITLE = NAME.' - Configuration Flux RSS';

require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$key = 0;
$categories = array();

$services = get_services(array('type' => Service::TYPE_ISOU, 'visible' => true));

foreach($services as $service){
	if($service->enable === '0' || $service->visible === '0'){
		continue;
	}

	if(!isset($categories[$service->idcategory])){
		$category = get_category($service->idcategory);
		if($category === FALSE){
			continue;
		}

		$categories[$service->idcategory] = $category;
		$categories[$service->idcategory]->services = array();
	}

	$categories[$service->idcategory]->services[] = $service;

	if(isset($_POST['keys'][$service->id])){
		$key += pow(2, $service->rsskey);
	}
}

if(isset($_POST['generer'])){
	if($key === 0){
		$rss_url = URL.'/rss.php';
	}else{
		$rss_url = URL.'/rss.php?key='.strtoupper(dechex($key));
	}
}else{
	$rss_url = NULL;
}

$smarty->assign('categories', $categories);
$smarty->assign('rss_url', $rss_url);

$TEMPLATE = 'public/rss_config.tpl';
