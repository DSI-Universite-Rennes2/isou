<?php

if(isset($PAGE_NAME[3]) && ctype_digit($PAGE_NAME[3])){
	$service = get_service($PAGE_NAME[3], UniversiteRennes2\Isou\Service::TYPE_ISOU);
}else{
	$service = FALSE;
}

if($service === FALSE){
	$_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

	header('Location: '.URL.'/index.php/services/isou');
	exit(0);
}elseif(isset($_POST['delete'])){
	$_POST = array_merge($_POST, $service->delete());

	if(!isset($_POST['errors'][0])){
		$_SESSION['messages'] = $_POST;

		header('Location: '.URL.'/index.php/services/isou');
		exit(0);
	}
}

$smarty->assign('service', $service);

$SUBTEMPLATE = 'services/isou_delete.tpl';

?>
