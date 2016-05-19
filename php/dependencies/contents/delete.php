<?php

if(isset($PAGE_NAME[7]) && ctype_digit($PAGE_NAME[5]) && ctype_digit($PAGE_NAME[7])){
	$dependency_group_content = get_dependency_group_content($PAGE_NAME[5], $PAGE_NAME[7]);
}else{
	$dependency_group_content = FALSE;
}

if($dependency_group_content === FALSE){
	$_SESSION['messages'] = array('errors' => array('Ce contenu n\'existe pas.'));

	header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
	exit(0);
}elseif(isset($_POST['delete'])){
	$_POST = array_merge($_POST, $dependency_group_content->delete());

	if(!isset($_POST['errors'][0])){
		$_SESSION['messages']['successes'] = $_POST['successes'];

		header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
		exit(0);
	}
}

$smarty->assign('service', $service);
$smarty->assign('dependency_group_content', $dependency_group_content);

$TEMPLATE = 'dependencies/contents/delete.tpl';

?>
