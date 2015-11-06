<?php

$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/configuration.css');

if(isset($_GET['type']) && $_GET['type'] == 'advanced'){
	require PRIVATE_PATH.'/php/configuration/advanced.php';
}elseif(isset($_GET['type']) && $_GET['type'] == 'changelog'){
	require PRIVATE_PATH.'/php/configuration/changelog.php';
}else{
	require PRIVATE_PATH.'/php/configuration/general.php';
}

$smarty->assign('CFG', $CFG);
$smarty->assign('error', $_POST['error']);

?>
