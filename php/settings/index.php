<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\SimpleMenu;

$TITLE .= ' - Administration des services';

if (isset($PAGE_NAME[1]) === false) {
    $PAGE_NAME[1] = '';
}

switch ($PAGE_NAME[1]) {
    case 'authentification':
        require PRIVATE_PATH.'/php/settings/authentification.php';
        break;
    case 'changelog':
        require PRIVATE_PATH.'/php/settings/changelog.php';
        break;
    case 'diagnostics':
        require PRIVATE_PATH.'/php/settings/diagnostics.php';
        break;
    case 'monitoring':
        require PRIVATE_PATH.'/php/settings/monitoring.php';
        break;
    case 'notifications':
        require PRIVATE_PATH.'/php/settings/notifications.php';
        break;
    case 'proxy':
        require PRIVATE_PATH.'/php/settings/proxy.php';
        break;
    case 'rapport':
        require PRIVATE_PATH.'/php/settings/report.php';
        break;
    case 'utilisateurs':
        require PRIVATE_PATH.'/php/settings/users.php';
        break;
    case 'apparence':
        require PRIVATE_PATH.'/php/settings/appearance.php';
        break;
    case 'general':
    default:
        require PRIVATE_PATH.'/php/settings/general.php';
}

$services_menu = array();
$services_menu['general'] = new SimpleMenu('Général', 'Configuration générale', URL.'/index.php/configuration/general');
$services_menu['proxy'] = new SimpleMenu('Proxy HTTP', 'Configuration d\'un proxy web', URL.'/index.php/configuration/proxy');
$services_menu['apparence'] = new SimpleMenu('Apparence', 'Configuration des vues', URL.'/index.php/configuration/apparence');
$services_menu['authentification'] = new SimpleMenu('Authentification', 'Gestion de l\'authentification', URL.'/index.php/configuration/authentification');
$services_menu['utilisateurs'] = new SimpleMenu('Utilisateurs', 'Afficher les utilisateurs', URL.'/index.php/configuration/utilisateurs');
$services_menu['monitoring'] = new SimpleMenu('Monitoring', 'Gestion des plugins de monitoring', URL.'/index.php/configuration/monitoring');
$services_menu['notifications'] = new SimpleMenu('Notifications web', 'Gestion des notifications', URL.'/index.php/configuration/notifications');
$services_menu['rapport'] = new SimpleMenu('Rapport', 'Gestion du rapport quotidien', URL.'/index.php/configuration/rapport');
$services_menu['diagnostics'] = new SimpleMenu('Diagnostics', 'Diagnostics de l\'application ISOU', URL.'/index.php/configuration/diagnostics');
$services_menu['changelog'] = new SimpleMenu('Changelog', 'Afficher le changelog', URL.'/index.php/configuration/changelog');

if (isset($services_menu[$PAGE_NAME[1]]) === true) {
    $services_menu[$PAGE_NAME[1]]->selected = true;
} else {
    $services_menu['general']->selected = true;
}

$smarty->assign('services_menu', $services_menu);

$smarty->assign('SUBTEMPLATE', $SUBTEMPLATE);

$TEMPLATE = 'settings/index.tpl';
