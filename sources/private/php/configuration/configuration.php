<?php

$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/configuration.css" media="screen" />';
$script = '';

// $script = '<script type="text/javascript" src="'.URL.'/scripts/jquery-min.js"></script>';
if(isset($_GET['type']) && $_GET['type'] == 'advanced'){
	require PRIVATE_PATH.'/php/configuration/advanced.php';
//	$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_services_nagios.js"></script>';
}elseif(isset($_GET['type']) && $_GET['type'] == 'changelog'){
	require PRIVATE_PATH.'/php/configuration/changelog.php';
}else{
	require PRIVATE_PATH.'/php/configuration/general.php';
//	$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_services_isou.js"></script>';
}

$smarty->assign('CFG', $CFG);
$smarty->assign('error', $_POST['error']);

?>
