<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\SimpleMenu;
use UniversiteRennes2\Isou\Plugin;

$TITLE .= ' - Configuration de l\'authentification';

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = 'manual';
}

$submenus = array();
$modules = Plugin::get_records(array('type' => 'authentication'));
foreach ($modules as $module) {
    // Set up menu.
    $submenus[$module->codename] = new SimpleMenu($module->name, '', URL.'/index.php/configuration/authentification/'.$module->codename);

    // Set up page.
    if ($module->codename === $PAGE_NAME[2]) {
        $plugin = clone $module;
        $submenus[$module->codename]->selected = true;
    }
}

// Set up fallback page.
if (isset($plugin) === false) {
    $plugin = Plugin::get_record(array('codename' => 'manual'));
    $submenus[$plugin->codename]->selected = true;
}

// Load page.
require PRIVATE_PATH.'/plugins/authentication/'.$plugin->codename.'/php/settings.php';

$smarty->assign('submenus', $submenus);

$smarty->assign('AUTHENTICATION_TEMPLATE', $AUTHENTICATION_TEMPLATE);

$SUBTEMPLATE = 'settings/authentication.tpl';
