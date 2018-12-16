<?php

use Isou\Helpers\SimpleMenu;
use UniversiteRennes2\Isou\Plugin;

$TITLE .= ' - Configuration de logiciels de monitoring utilisés en arrière-plan d\'isou';

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = 'isou';
}

$submenus = array();
$modules = Plugin::get_records(array('type' => 'monitoring'));
foreach ($modules as $module) {
    // Set up menu.
    $submenus[$module->codename] = new SimpleMenu($module->name, '', URL.'/index.php/configuration/monitoring/'.$module->codename);

    // Set up page.
    if ($module->codename === $PAGE_NAME[2]) {
        $plugin = $module;
        $submenus[$module->codename]->selected = true;
    }
}

// Set up fallback page.
if (isset($plugin) === false) {
    $plugin = Plugin::get_record(array('codename' => 'isou'));
}

// Load page.
require PRIVATE_PATH.'/plugins/monitoring/'.$plugin->codename.'/php/settings.php';

$smarty->assign('submenus', $submenus);

$smarty->assign('MONITORING_TEMPLATE', $MONITORING_TEMPLATE);

$SUBTEMPLATE = 'settings/monitoring.tpl';
