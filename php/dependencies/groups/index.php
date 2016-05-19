<?php

if(isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])){
	$service = get_service($PAGE_NAME[2], UniversiteRennes2\Isou\Service::TYPE_ISOU);
}else{
	$service = FALSE;
}

if($service === FALSE){
	$_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

	header('Location: '.URL.'/index.php/dependances');
	exit(0);
}

if(isset($PAGE_NAME[7])){
	// group contents
	switch($PAGE_NAME[3]){
		case 'delete':
			require PRIVATE_PATH.'/php/dependencies/contents/delete.php';
			break;
		case 'edit':
			require PRIVATE_PATH.'/php/dependencies/contents/edit.php';
			break;
		default:
			require PRIVATE_PATH.'/php/dependencies/contents/add.php';

	}
}elseif(isset($PAGE_NAME[5])){
	// groups
	switch($PAGE_NAME[3]){
		case 'delete':
			require PRIVATE_PATH.'/php/dependencies/groups/delete.php';
			break;
		case 'duplicate':
			require PRIVATE_PATH.'/php/dependencies/groups/duplicate.php';
			break;
		default:
			require PRIVATE_PATH.'/php/dependencies/groups/edit.php';
	}
}else{
	require PRIVATE_PATH.'/php/dependencies/groups/list.php';
}

?>
