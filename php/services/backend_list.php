<?php

$services = get_services(array('type' => $backend->type));

foreach($services as $service){
	$service->notes = array();

	$service->get_reverse_dependencies();
	if(!isset($service->reverse_dependencies[0])){
		$service->notes[] = 'Service non utilisé';
	}
}

$smarty->assign('services', $services);

$SUBTEMPLATE = 'services/backend_list.tpl';

