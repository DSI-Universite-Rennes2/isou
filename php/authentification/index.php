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
            foreach (Plugin::get_records(array('type' => 'authentification', 'active' => true)) as $plugin) {
                $count_plugins++;
                $plugins[$plugin->codename] = $plugin;
            }

            if ($count_plugins === 1) {
                $PAGE_NAME[1] = key($plugins);
            }

            if (isset($PAGE_NAME[1], $plugins[$PAGE_NAME[1]]) === true) {
                $plugin = $plugins[$PAGE_NAME[1]];

                require PRIVATE_PATH.'/plugins/authentification/'.$plugin->codename.'/lib.php';

                $form = authentification_login($plugin);
            }

            $smarty->assign('form', $form);
            $smarty->assign('plugins', $plugins);
            $smarty->assign('count_plugins', $count_plugins);

            $TEMPLATE = 'authentification/index.tpl';
        }
        break;
    case 'deconnexion':
        if (isset($USER) === true && $USER !== false) {
            $authentification_lib = PRIVATE_PATH.'/plugins/authentification/'.$USER->authentification.'/lib.php';

            $plugin = Plugin::get_record(array('type' => 'authentification', 'codename' => $USER->authentification));
            if (is_file($authentification_lib) === true) {
                require $authentification_lib;
            } else {
                require PRIVATE_PATH.'/plugins/authentification/manual/lib.php';
            }

            authentification_logout($plugin);
        }
        break;
}
