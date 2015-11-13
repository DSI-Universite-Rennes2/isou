<?php

use UniversiteRennes2\Isou;

if(isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])){
	$category = get_category($PAGE_NAME[2]);
}else{
	$category = FALSE;
}

if($category === FALSE){
	$_SESSION['messages'] = array('errors' => array('Cette catégorie n\'existe pas.'));

	header('Location: '.URL.'/index.php/categories');
	exit(0);
}elseif(isset($_POST['delete'])){
	$_POST = array_merge($_POST, $category->delete());

	if(!isset($_POST['errors'][0])){
		$_SESSION['messages']['successes'] = $_POST['successes'];

		header('Location: '.URL.'/index.php/categories');
		exit(0);
	}
}

$smarty->assign('category', $category);

$TEMPLATE = 'categories/delete.tpl';

?>
