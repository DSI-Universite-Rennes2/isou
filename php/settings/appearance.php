<?php

use Isou\Helpers\SimpleMenu;
use UniversiteRennes2\Isou\Plugin;

$TITLE .= ' - Configuration des vues';

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = $CFG['menu_default'];
}

$submenus = array();
$modules = Plugin::get_records(array('type' => 'view'));
foreach ($modules as $module) {
    // Set up menu.
    $submenus[$module->codename] = new SimpleMenu($module->name, '', URL.'/index.php/configuration/apparence/'.$module->codename);

    // Set up page.
    if ($module->codename === $PAGE_NAME[2]) {
        $plugin = clone $module;
        $submenus[$module->codename]->selected = true;
    }
}

// Set up fallback page.
if (isset($plugin) === false) {
    $plugin = Plugin::get_record(array('codename' => 'list'));
    $submenus[$plugin->codename]->selected = true;
}

// Load page.
require PRIVATE_PATH.'/plugins/view/'.$plugin->codename.'/php/settings.php';

$smarty->assign('submenus', $submenus);

$smarty->assign('VIEW_TEMPLATE', $VIEW_TEMPLATE);

$SUBTEMPLATE = 'settings/appearance.tpl';
