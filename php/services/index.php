<?php

use Isou\Helpers\SimpleMenu;
use UniversiteRennes2\Isou\Plugin;

$TITLE .= ' - Administration des services';

// Set up menu.
$submenus = array();
$plugins = Plugin::get_records();
foreach ($plugins as $plugin) {
    $submenus[$plugin->codename] = new SimpleMenu('Services '.$plugin->name, '', URL.'/index.php/services/'.$plugin->codename);
}

// Plugins.
if (isset($PAGE_NAME[1]) === false) {
    $PAGE_NAME[1] = 'isou';
}

$plugin = 'PLUGIN_'.strtoupper($PAGE_NAME[1]);
if (defined($plugin) === false) {
    $PAGE_NAME[1] = 'isou';
}
$plugin = constant('PLUGIN_'.strtoupper($PAGE_NAME[1]));

$submenus[$PAGE_NAME[1]]->selected = true;

$plugin = Plugin::get_record(array('id' => $plugin));
require PRIVATE_PATH.'/plugins/'.$plugin->codename.'/index.php';

$smarty->assign('submenus', $submenus);

$smarty->assign('SUBTEMPLATE', $SUBTEMPLATE);

$TEMPLATE = 'services/services.tpl';
