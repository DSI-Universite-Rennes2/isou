<?php

use UniversiteRennes2\Isou\Service;

if(isset($PAGE_NAME[3]) && ctype_digit($PAGE_NAME[3])){
	$service = get_service(array('id' => $PAGE_NAME[3], 'type' => Service::TYPE_ISOU));
}else{
	$service = FALSE;
}

if($service === FALSE){
	$service = new Service();
	$service->idtype = Service::TYPE_ISOU;
}

require_once PRIVATE_PATH.'/libs/categories.php';
$categories = get_categories_sorted_by_id();

if(isset($_POST['category'], $_POST['name'], $_POST['url'], $_POST['visible'])){
	$service->idcategory = $_POST['category'];
	$service->name = $_POST['name'];
	$service->url = $_POST['url'];
	$service->visible = $_POST['visible'];

	$_POST['errors'] = $service->check_data($categories);
	if(!isset($_POST['errors'][0])){
		$_POST = array_merge($_POST, $service->save());
		if(!isset($_POST['errors'][0])){
			$_SESSION['messages']['successes'] = $_POST['successes'];

			header('Location: '.URL.'/index.php/services/isou');
			exit(0);
		}
	}
}

$smarty->assign('yesno', array('1' => 'Afficher', '0' => 'Masquer'));

$smarty->assign('service', $service);

$smarty->assign('categories', $categories);

$SUBTEMPLATE = 'services/isou_edit.tpl';

?>
