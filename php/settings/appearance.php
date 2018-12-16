<?php

$TITLE .= ' - Configuration de l\'apparence';

$menus = get_menu_sorted_by_url();
$menus_active = array_keys(get_active_menu_sorted_by_url());

$themes = array();
$themes_path = PUBLIC_PATH.'/themes';
if ($handle = opendir($themes_path)) {
    while (($entry = readdir($handle)) !== false) {
        if (ctype_alnum($entry) === true && is_dir($themes_path.'/'.$entry) === true) {
            $themes[$entry] = $entry;
        }
    }
    closedir($handle);
}

// Hack pour pré-initialiser la variable site_url.
if (empty($CFG['site_url']) === true) {
    $CFG['site_url'] = URL.'/';
}

foreach (array('site_name', 'site_header', 'site_url', 'tolerance', 'menu_default', 'theme') as $key) {
    if (isset($_POST[$key]) === true) {
        $value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
        if ($value !== $CFG[$key]) {
            if (set_configuration($key, $value) === true) {
                $CFG[$key] = $value;
            }
        }
    }
}

if (isset($_POST['menus_active']) && is_array($_POST['menus_active'])) {
    $sql = "UPDATE menus SET active=0";
    $query = $DB->prepare($sql);
    $query->execute();

    $active = array();

    foreach ($_POST['menus_active'] as $menu_active) {
        $sql = "UPDATE menus SET active=1 WHERE url=?";
        $query = $DB->prepare($sql);
        $query->execute(array($menu_active));

        $active[] = $menu_active;
    }

    if ($active !== $menus_active) {
        $menus_active = $active;

        $_POST['successes'][] = 'Mise à jour des menus';
    }

    $MENU = get_active_menu();
}

$smarty->assign('menus', $menus);
$smarty->assign('themes', $themes);
$smarty->assign('menus_active', $menus_active);

$SUBTEMPLATE = 'settings/appearance.tpl';
