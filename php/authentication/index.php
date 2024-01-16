<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Plugin;

switch ($PAGE_NAME[0]) {
    case 'connexion':
        if (isset($_SESSION['username']) === false) {
            $form = '';

            $plugins = array();
            $count_plugins = 0;
            foreach (Plugin::get_records(array('type' => 'authentication', 'active' => true)) as $plugin) {
                $count_plugins++;
                $plugins[$plugin->codename] = $plugin;
            }

            if ($count_plugins === 1) {
                $PAGE_NAME[1] = key($plugins);
            }

            if (isset($PAGE_NAME[1], $plugins[$PAGE_NAME[1]]) === true) {
                $plugin = $plugins[$PAGE_NAME[1]];

                require PRIVATE_PATH.'/plugins/authentication/'.$plugin->codename.'/lib.php';

                $form = authentication_login($plugin);
            }

            $smarty->assign('form', $form);
            $smarty->assign('plugins', $plugins);
            $smarty->assign('count_plugins', $count_plugins);

            $TEMPLATE = 'authentication/index.tpl';
        }
        break;
    case 'deconnexion':
        if (isset($USER) === true && $USER !== false) {
            $authentication_lib = PRIVATE_PATH.'/plugins/authentication/'.$USER->authentication.'/lib.php';

            $plugin = Plugin::get_record(array('type' => 'authentication', 'codename' => $USER->authentication));
            if (is_file($authentication_lib) === true) {
                require $authentication_lib;
            } else {
                require PRIVATE_PATH.'/plugins/authentication/manual/lib.php';
            }

            authentication_logout($plugin);
        }
        break;
}
