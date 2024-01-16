<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\SimpleMenu;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\State;
use UniversiteRennes2\Isou\User;

session_name('isou');
session_start();

if (is_file(__DIR__.'/../config.php') === false) {
    echo 'L\'application ne semble pas être installée.'.
        ' Veuillez suivre la <a href="https://github.com/DSI-Universite-Rennes2/isou#installation-et-mise-%C3%A0-jour" target="_blank">procédure d\'installation</a>.';
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

// Définit le titre de la page HTML.
if (isset($CFG['site_name']) === true) {
    $TITLE = $CFG['site_name'];
} else {
    $TITLE = 'Isou';
}

$USER = false;
if (isset($_SESSION['username'], $_SESSION['authentication']) === true) {
    $USER = User::get_record(array('username' => $_SESSION['username'], 'authentication' => $_SESSION['authentication']));
} elseif (defined('DEV') === true && DEV === true) {
    $USER = User::get_record(array('username' => 'isou', 'authentication' => 'manual'));
}

// Calcule la route de la page.
$PAGE_NAME = explode('/', get_page_name());

// Load states.
$STATES = State::get_records();

// Load menu.
$MENUS = new stdClass();
$MENUS->public = SimpleMenu::get_public_menus();
$MENUS->administration = array();

if (has_new_version() === true) {
    // Display maintenance page.
    $TEMPLATE = 'common/update.tpl';
} else {
    // Display default pages.
    if (in_array($PAGE_NAME[0], array('connexion', 'deconnexion'), $strict = true) === true) {
        require PRIVATE_PATH.'/php/authentication/index.php';
    }

    if (isset($USER->admin) === true && empty($USER->admin) === false) {
        $MENUS->administration = SimpleMenu::get_adminitration_menus();

        // TODO: À supprimer, lorsque l'implémentation du changement de mot de passe sera faite.
        $local_auth = Plugin::get_record(array('codename' => 'manual', 'type' => 'authentication', 'active' => true));
        $smarty->assign('security_local_auth', ($local_auth !== false));
    }

    // Routing.
    if (isset($TEMPLATE) === false) {
        if (isset($MENUS->administration[$PAGE_NAME[0]]) === true) {
            // Si l'utilisateur est admin.
            $MENUS->administration[$PAGE_NAME[0]]->selected = true;

            switch ($PAGE_NAME[0]) {
                case 'evenements':
                    require PRIVATE_PATH.'/php/events/index.php';
                    break;
                case 'annonce':
                    require PRIVATE_PATH.'/php/announcement/index.php';
                    break;
                case 'statistiques':
                    require PRIVATE_PATH.'/php/history/index.php';
                    break;
                case 'services':
                    require PRIVATE_PATH.'/php/services/index.php';
                    break;
                case 'dependances':
                    require PRIVATE_PATH.'/php/dependencies/index.php';
                    break;
                case 'categories':
                    require PRIVATE_PATH.'/php/categories/index.php';
                    break;
                case 'configuration':
                    require PRIVATE_PATH.'/php/settings/index.php';
                    break;
                case 'aide':
                    require PRIVATE_PATH.'/php/help/index.php';
            }
        }

        if (isset($TEMPLATE) === false) {
            require PRIVATE_PATH.'/php/views/index.php';
        }
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
$smarty->assign('MENUS', $MENUS);

if (isset($ANNOUNCEMENT) === true && $ANNOUNCEMENT !== false) {
    $smarty->assign('ANNOUNCEMENT', $ANNOUNCEMENT);
}

$smarty->assign('TEMPLATE', $TEMPLATE);
$smarty->display('common/base.tpl');

// Close pdo connection.
$DB = null;

unset($_SESSION['messages']);
