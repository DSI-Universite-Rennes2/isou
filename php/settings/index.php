<?php

use Isou\Helpers\SimpleMenu;

$TITLE .= ' - Administration des services';

if (!isset($PAGE_NAME[1])) {
    $PAGE_NAME[1] = '';
}

switch ($PAGE_NAME[1]) {
    case 'authentification':
        require PRIVATE_PATH.'/php/settings/authentification.php';
        break;
    case 'changelog':
        require PRIVATE_PATH.'/php/settings/changelog.php';
        break;
    case 'diagnostiques':
        require PRIVATE_PATH.'/php/settings/diagnostics.php';
        break;
    case 'informations':
        require PRIVATE_PATH.'/php/settings/information.php';
        break;
    case 'monitoring':
        require PRIVATE_PATH.'/php/settings/monitoring.php';
        break;
    case 'notifications':
        require PRIVATE_PATH.'/php/settings/notifications.php';
        break;
    case 'apparence':
    default:
        require PRIVATE_PATH.'/php/settings/appearance.php';
}

$services_menu = array();
$services_menu['apparence'] = new SimpleMenu('Apparence', 'Configuration de l\'apparence, des menus, etc', URL.'/index.php/configuration/apparence');
$services_menu['authentification'] = new SimpleMenu('Authentification', 'Gestion de l\'authentification', URL.'/index.php/configuration/authentification');
$services_menu['monitoring'] = new SimpleMenu('Monitoring', 'Gestion des plugins de monitoring', URL.'/index.php/configuration/monitoring');
$services_menu['notifications'] = new SimpleMenu('Notifications', 'Gestion des notifications', URL.'/index.php/configuration/notifications');
$services_menu['diagnostiques'] = new SimpleMenu('Diagnostiques', 'Procédure de diagnostique', URL.'/index.php/configuration/diagnostiques');
$services_menu['informations'] = new SimpleMenu('Informations', 'Configuration avancée', URL.'/index.php/configuration/informations');
$services_menu['changelog'] = new SimpleMenu('Changelog', 'Afficher le changelog', URL.'/index.php/configuration/changelog');

if (isset($services_menu[$PAGE_NAME[1]])) {
    $services_menu[$PAGE_NAME[1]]->selected = true;
} else {
    $services_menu['apparence']->selected = true;
}

$smarty->assign('services_menu', $services_menu);

$smarty->assign('SUBTEMPLATE', $SUBTEMPLATE);

$TEMPLATE = 'settings/index.tpl';
