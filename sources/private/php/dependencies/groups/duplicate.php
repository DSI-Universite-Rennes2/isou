<?php

if(isset($PAGE_NAME[5]) && ctype_digit($PAGE_NAME[5])){
	$dependency_group = get_dependency_group($PAGE_NAME[5]);
}else{
	$dependency_group = FALSE;
}

if($dependency_group === FALSE){
	$_SESSION['messages'] = array('errors' => array('Ce groupe n\'existe pas.'));

	header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
	exit(0);
}elseif(isset($_POST['duplicate'])){
	$_POST = array_merge($_POST, $dependency_group->duplicate());

	if(!isset($_POST['errors'][0])){
		$_SESSION['messages']['successes'] = $_POST['successes'];

		header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
		exit(0);
	}
}

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);

$TEMPLATE = 'dependencies/groups/duplicate.tpl';

?>
