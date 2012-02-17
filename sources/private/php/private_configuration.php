<?php

$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/configuration.css" media="screen" />';
$script = '';
$title = NAME.' - Configuration'; // remove

// $script = '<script type="text/javascript" src="'.URL.'/js/jquery-min.js"></script>';
if(isset($_GET['type']) && $_GET['type'] == 'advanced'){
	require BASE.'/php/private_configuration_advanced.php';
//	$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_services_nagios.js"></script>';
}elseif(isset($_GET['type']) && $_GET['type'] == 'changelog'){
	require BASE.'/php/private_configuration_changelog.php';
}else{
	require BASE.'/php/private_configuration_general.php';
//	$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_services_isou.js"></script>';
}

$smarty->assign('CFG', $CFG);
$smarty->assign('error', $_POST['error']);

?>
