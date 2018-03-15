<?php

require_once PRIVATE_PATH.'/libs/services.php';

$TITLE .= ' - Administration des services';

if(!isset($PAGE_NAME[1])){
	$PAGE_NAME[1] = '';
}

switch($PAGE_NAME[1]){
	case 'authentification':
		require PRIVATE_PATH.'/php/configuration/authentification.php';
		break;
	case 'changelog':
		require PRIVATE_PATH.'/php/configuration/changelog.php';
		break;
	case 'diagnostiques':
		require PRIVATE_PATH.'/php/configuration/diagnostics.php';
		break;
	case 'informations':
		require PRIVATE_PATH.'/php/configuration/information.php';
		break;
	case 'plugins':
		require PRIVATE_PATH.'/php/configuration/plugins.php';
		break;
	case 'notifications':
		require PRIVATE_PATH.'/php/configuration/notifications.php';
		break;
	case 'apparence':
	default:
		require PRIVATE_PATH.'/php/configuration/appearance.php';
}

$services_menu = array();
$services_menu['apparence'] = new Isou\Helpers\SimpleMenu('Apparence', 'Configuration de l\'apparence, des menus, etc', URL.'/index.php/configuration/apparence');
$services_menu['authentification'] = new Isou\Helpers\SimpleMenu('Authentification', 'Gestion de l\'authentification', URL.'/index.php/configuration/authentification');
$services_menu['plugins'] = new Isou\Helpers\SimpleMenu('Plugins', 'Gestion des plugins', URL.'/index.php/configuration/plugins');
$services_menu['notifications'] = new Isou\Helpers\SimpleMenu('Notifications', 'Gestion des notifications', URL.'/index.php/configuration/notifications');
$services_menu['diagnostiques'] = new Isou\Helpers\SimpleMenu('Diagnostiques', 'Procédure de diagnostique', URL.'/index.php/configuration/diagnostiques');
$services_menu['informations'] = new Isou\Helpers\SimpleMenu('Informations', 'Configuration avancée', URL.'/index.php/configuration/informations');
$services_menu['changelog'] = new Isou\Helpers\SimpleMenu('Changelog', 'Afficher le changelog', URL.'/index.php/configuration/changelog');

if(isset($services_menu[$PAGE_NAME[1]])){
	$services_menu[$PAGE_NAME[1]]->selected = TRUE;
}else{
	$services_menu['apparence']->selected = TRUE;
}

$smarty->assign('services_menu', $services_menu);

$smarty->assign('SUBTEMPLATE', $SUBTEMPLATE);

$TEMPLATE = 'configuration/configuration.tpl';
