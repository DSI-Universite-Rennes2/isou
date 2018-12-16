<?php

use UniversiteRennes2\Isou\Announcement;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\State;
use UniversiteRennes2\Isou\User;

session_name('isou');
session_start();

if (is_file(__DIR__.'/../config.php') === false) {
    echo 'L\'application ne semble pas être installée.'.
        ' Merci d\'exécuter en ligne de commande le script install.php qui se trouve dans ./sources/private/upgrade.';
    exit(1);
}

require __DIR__.'/../config.php';

$smarty = new Smarty();
$smarty->setTemplateDir(PRIVATE_PATH.'/html/');
$smarty->setCompileDir(PRIVATE_PATH.'/cache/smarty/');

// Tableau contenant toutes les URL de javascript à charger.
$SCRIPTS = array();

// Tableau contenant toutes les URL de css à charger.
$STYLES = array();

require PRIVATE_PATH.'/php/common/database.php';

require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

// Charge les plugins.
$plugins = get_plugins();

// Définis le titre de la page HTML.
if (isset($CFG['site_name']) === true) {
    $TITLE = $CFG['site_name'];
} else {
    $TITLE = 'Isou';
}

$USER = false;
if (isset($_SESSION['username'], $_SESSION['authentification']) === true) {
    $USER = User::get_record(array('username' => $_SESSION['username'], 'authentification' => $_SESSION['authentification']));
}

if (has_new_version() === true) {
    // Display maintenance page.
    $TEMPLATE = 'public/update.tpl';

    $MENU = array();
    $STATES = array();
} else {
    // Display default pages.
    $PAGE_NAME = explode('/', get_page_name());
    if (in_array($PAGE_NAME[0], array('connexion', 'deconnexion'), $strict = true) === true) {
        require PRIVATE_PATH.'/php/authentification/index.php';
    }

    // Load states.
    $STATES = State::get_records();

    // Load menu.
    require PRIVATE_PATH.'/libs/menu.php';

    $MENU = get_active_menu();
    if (isset($USER->admin) === true && empty($USER->admin) === false) {
        $ADMINISTRATION_MENU = get_administration_menu();
    }

    // Routing.
    if (isset($TEMPLATE) === false) {
        if (isset($MENU[$PAGE_NAME[0]]) === true) {
            $current_page = $MENU[$PAGE_NAME[0]];
        } elseif (isset($ADMINISTRATION_MENU[$PAGE_NAME[0]]) === true) {
            $current_page = $ADMINISTRATION_MENU[$PAGE_NAME[0]];
        } else {
            if (isset($CFG['menu_default'], $MENU[$CFG['menu_default']]) === true) {
                $current_page = $MENU[$CFG['menu_default']];
            } else {
                $current_page = current($MENU);
            }
        }
        $current_page->selected = true;

        // Load announcement.
        if (isset($MENU[$current_page->url]) === true) {
            $ANNOUNCEMENT = Announcement::get_record(array('empty' => false, 'visible' => true));
        }

        require PRIVATE_PATH.$current_page->model;
    }
}

if (isset($CFG['theme']) === false || is_file(PUBLIC_PATH.'/themes/'.$CFG['theme'].'/theme.php') === false) {
    $CFG['theme'] = 'bootstrap';
}

require PUBLIC_PATH.'/themes/'.$CFG['theme'].'/theme.php';

$smarty->assign('TITLE', $TITLE);
$smarty->assign('SCRIPTS', $SCRIPTS);
$smarty->assign('STYLES', $STYLES);
$smarty->assign('USER', $USER);
$smarty->assign('CFG', $CFG);
$smarty->assign('STATES', $STATES);
$smarty->assign('MENU', $MENU);

if (isset($ANNOUNCEMENT) === true && $ANNOUNCEMENT !== false) {
    $smarty->assign('ANNOUNCEMENT', $ANNOUNCEMENT);
}

if (isset($ADMINISTRATION_MENU) === true) {
    $smarty->assign('ADMINISTRATION_MENU', $ADMINISTRATION_MENU);
}

$smarty->assign('TEMPLATE', $TEMPLATE);
$smarty->display('common/base.tpl');

// Close pdo connection.
$DB = null;

unset($_SESSION['messages']);
