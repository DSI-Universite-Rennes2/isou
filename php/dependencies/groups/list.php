<?php

use UniversiteRennes2\Isou\Service;

if(isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])){
	$service = get_service(array('id' => $PAGE_NAME[2], 'type' => Service::TYPE_ISOU));
}else{
	$service = FALSE;
}

if($service === FALSE){
	$_SESSION['messages'] = array('errors' => array('Ce service n\'existe pas.'));

	header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
	exit(0);
}

$smarty->assign('service', $service);
$smarty->assign('groups', get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($PAGE_NAME[2]));

$TEMPLATE = 'dependencies/groups/list.tpl';

