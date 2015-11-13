<?php

if(isset($PAGE_NAME[7]) && ctype_digit($PAGE_NAME[5]) && ctype_digit($PAGE_NAME[7])){
	$dependency_group_content = get_dependency_group_content($PAGE_NAME[5], $PAGE_NAME[7]);
	$group = get_dependency_group($PAGE_NAME[5]);
	$backend = get_service($PAGE_NAME[7]);
}else{
	$dependency_group_content = FALSE;
}

if($dependency_group_content === FALSE){
	$_SESSION['messages'] = array('errors' => array('Ce contenu n\'existe pas.'));

	header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
	exit(0);
}

$options_states = array(1 => 'Orange', 2 => 'Rouge');

if(isset($_POST['servicestate'], $options_states[$_POST['servicestate']])){
	$_POST = array_merge($_POST, $dependency_group_content->change_state($_POST['servicestate']));
	if(!isset($_POST['errors'][0])){
		$_SESSION['messages'] = $_POST;

		header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
		exit(0);
	}
}

$smarty->assign('options_states', $options_states);

$smarty->assign('group', $group);
$smarty->assign('backend', $backend);
$smarty->assign('service', $service);
$smarty->assign('dependency_group_content', $dependency_group_content);

$TEMPLATE = 'dependencies/contents/edit.tpl';

?>
