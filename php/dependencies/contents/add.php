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
}

$options_states = array(1 => 'Orange', 2 => 'Rouge');
$options_services = get_services_sorted_by_idtype();

if(isset($_POST['servicestate'], $options_states[$_POST['servicestate']])){
	if(isset($_POST['services']) && is_array($_POST['services'])){
		$dependency_group->get_content_services_sorted_by_id();

		foreach($_POST['services'] as $idservice){
			if($idservice === $service->id){
				continue;
			}

			if(!isset($dependency_group->services[$idservice])){
				$content = new UniversiteRennes2\Isou\Dependency_Group_Content();
				$content->idgroup = $dependency_group->id;
				$content->idservice = $idservice;
				$content->servicestate = $_POST['servicestate'];

				$_POST = array_merge($_POST, $content->save());

			}
		}

		if(!isset($_POST['errors'][0])){
			$_SESSION['messages'] = $_POST;

			header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
			exit(0);
		}
	}
}

$smarty->assign('options_states', $options_states);
$smarty->assign('options_services', $options_services);

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);

$TEMPLATE = 'dependencies/contents/add.tpl';

?>
