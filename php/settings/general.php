<?php

$TITLE .= ' - Configuration générale';

$menus = array();
foreach ($MENUS->public as $menu) {
    $menus[$menu->path] = $menu->label;
}

$themes = array();
$themes_path = PUBLIC_PATH.'/themes';

$handle = opendir($themes_path);
if ($handle !== false) {
    while (($entry = readdir($handle)) !== false) {
        if (ctype_alnum($entry) === true && is_dir($themes_path.'/'.$entry) === true) {
            $themes[$entry] = $entry;
        }
    }
    closedir($handle);
}
ksort($themes);

// Hack pour pré-initialiser la variable site_url.
if (empty($CFG['site_url']) === true) {
    $CFG['site_url'] = URL.'/';
}

foreach (array('site_name', 'site_header', 'site_url', 'tolerance', 'theme') as $key) {
    if (isset($_POST[$key]) === true) {
        $value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
        if ($value !== $CFG[$key]) {
            if (set_configuration($key, $value) === true) {
                $CFG[$key] = $value;
            }
        }
    }
}

if (isset($_POST['menu_default'], $MENUS->public[$_POST['menu_default']]) === true) {
    if (set_configuration('menu_default', $_POST['menu_default']) === true) {
        $CFG['menu_default'] = $_POST['menu_default'];
    }
}

$smarty->assign('menus', $menus);
$smarty->assign('themes', $themes);

$SUBTEMPLATE = 'settings/general.tpl';
