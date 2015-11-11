<?php

require_once PRIVATE_PATH.'/libs/categories.php';

$categories = get_categories_sorted_by_id();

foreach($categories as $idcategory => $category_name){
	$categories[$idcategory] = new stdClass();
	$categories[$idcategory]->name = $category_name;
	$categories[$idcategory]->services = array();
}

$services = get_services(UniversiteRennes2\Isou\Service::TYPE_ISOU);
foreach($services as $service){
	$service->notes = array();

	if($service->visible === '0'){
		$service->notes[] = 'Service masqué sur les pages publiques';
	}

	if($service->locked === '1'){
		$service->notes[] = 'Service dont l\'état est verrouillé';
	}

	$service->get_dependencies();
	if(!isset($service->dependencies[0])){
		$service->notes[] = 'Service sans dépendance';
	}

	$categories[$service->idcategory]->services[] = $service;
}

$smarty->assign('categories', $categories);

$SUBTEMPLATE = 'services/isou_list.tpl';

?>
