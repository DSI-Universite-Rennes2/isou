<?php

if(isset($PAGE_NAME[3]) && ctype_digit($PAGE_NAME[3])){
	$service = get_service($PAGE_NAME[3], $backend->type);
}else{
	$service = FALSE;
}

if($service === FALSE){
	$_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

	header('Location: '.URL.'/index.php/services/'.$backend->url);
	exit(0);
}elseif(isset($_POST['delete'])){
	$_POST = array_merge($_POST, $service->delete());

	if(isset($_POST['successes'][0])){
		$_SESSION['messages'] = $_POST;

		header('Location: '.URL.'/index.php/services/'.$backend->url);
		exit(0);
	}
}

$smarty->assign('service', $service);

$SUBTEMPLATE =  'services/backend_delete.tpl';

