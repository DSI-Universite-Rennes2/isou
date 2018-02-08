<?php

use UniversiteRennes2\Isou\Service;

if(isset($PAGE_NAME[3]) && ctype_digit($PAGE_NAME[3])){
	$service = get_service(array('id' => $PAGE_NAME[3], 'type' => Service::TYPE_ISOU));
}else{
	$service = FALSE;
}

if($service === FALSE){
	$_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

	header('Location: '.URL.'/index.php/services/isou');
	exit(0);
}

$sql = "SELECT DISTINCT s.id, s.name, s.state".
	" FROM services s".
	" JOIN dependencies_groups_content dgc ON s.id = dgc.idservice".
	" JOIN dependencies_groups dg ON dg.id = dgc.idgroup".
	" WHERE dg.idservice = ?";
$query = $DB->prepare($sql);
$query->execute(array($service->id));
$service->dependencies = $query->fetchAll(PDO::FETCH_OBJ);

$smarty->assign('service', $service);

$SUBTEMPLATE = 'services/isou_inspect.tpl';
