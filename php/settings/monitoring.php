<?php

use Isou\Helpers\SimpleMenu;
use UniversiteRennes2\Isou\Plugin;

$TITLE .= ' - Configuration de logiciels de monitoring utilisés en arrière-plan d\'isou';

// Set up menu.
$submenus = array();
$plugins = Plugin::get_records();
foreach ($plugins as $plugin) {
    $submenus[$plugin->codename] = new SimpleMenu($plugin->name, '', URL.'/index.php/configuration/monitoring/'.$plugin->codename);
}

// Plugins.
if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = 'isou';
}

$plugin = 'PLUGIN_'.strtoupper($PAGE_NAME[2]);
if (defined($plugin) === false) {
    $PAGE_NAME[2] = 'isou';
}
$plugin = constant('PLUGIN_'.strtoupper($PAGE_NAME[2]));

$submenus[$PAGE_NAME[2]]->selected = true;

$plugin = Plugin::get_record(array('id' => $plugin));
require PRIVATE_PATH.'/plugins/monitoring/'.$plugin->codename.'/php/settings.php';

$smarty->assign('submenus', $submenus);

$smarty->assign('MONITORING_TEMPLATE', $MONITORING_TEMPLATE);

$SUBTEMPLATE = 'settings/monitoring.tpl';
