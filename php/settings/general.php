<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

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

$options_yes_no = array(
    '1' => 'Oui',
    '0' => 'Non',
);

foreach (array('site_name', 'site_header', 'site_url', 'theme', 'check_updates_enabled', 'gather_statistics_enabled') as $key) {
    if (isset($_POST[$key]) === true) {
        $value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
        if ($value !== $CFG[$key]) {
            if (set_configuration($key, $value) === true) {
                $CFG[$key] = $value;

                if ($key === 'check_updates_enabled' && empty($value) === false) {
                    $yesterday = new DateTime('-24 hours');
                    set_configuration('last_update_check', $yesterday->format(DATE_RFC3339));
                }
            }
        }
    }
}

// Hack pour pré-initialiser la variable site_url.
if (empty($CFG['site_url']) === true) {
    $CFG['site_url'] = URL.'/';
}

if (isset($_POST['menu_default'], $MENUS->public[$_POST['menu_default']]) === true) {
    if (set_configuration('menu_default', $_POST['menu_default']) === true) {
        $CFG['menu_default'] = $_POST['menu_default'];
    }
}

$smarty->assign('menus', $menus);
$smarty->assign('options_yes_no', $options_yes_no);
$smarty->assign('themes', $themes);

$SUBTEMPLATE = 'settings/general.tpl';
