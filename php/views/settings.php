<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\SimpleMenu;

$options_yes_no = array(
    '1' => 'Oui',
    '0' => 'Non',
);

if (isset($_POST['plugin_view_enable'], $options_yes_no[$_POST['plugin_view_enable']]) === true) {
    if ($_POST['plugin_view_enable'] !== $plugin->active) {
        if ($_POST['plugin_view_enable'] === '0') {
            if ($CFG['menu_default'] === $plugin->codename) {
                $_POST['errors'][] = 'Il n\'est pas possible de désactiver cette vue, car cette vue est utilisé par défaut.';
            }

            $count_views = 0;
            foreach ($modules as $module) {
                if ($module->active === '1' && $module->codename !== $plugin->codename) {
                    $count_views++;
                }
            }

            if ($count_views === 0) {
                $_POST['errors'][] = 'Il n\'est pas possible de désactiver cette vue, car cette vue est la dernière vue activée.';
            }
        }

        if (isset($_POST['errors'][0]) === false) {
            $plugin->active = $_POST['plugin_view_enable'];
            $plugin->save();

            if ($plugin->active === '1') {
                $_POST['successes'][] = 'Vue "'.$plugin->name.'" activée.';
            } else {
                $_POST['successes'][] = 'Vue "'.$plugin->name.'" désactivée.';
            }
        }
    }
}

if (isset($_POST['plugin_view_label']) === true) {
    if ($plugin->settings->label !== $_POST['plugin_view_label']) {
        $plugin->settings->label = $_POST['plugin_view_label'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Champ "Libellé de l\'onglet" enregistré.';
    }
}

if (isset($_POST['plugin_view_route']) === true) {
    if ($plugin->settings->route !== $_POST['plugin_view_route']) {
        $route = $_POST['plugin_view_route'];
        if (isset($MENUS->public[$route]) === true || isset($MENUS->administration[$route]) === true) {
            $_POST['errors'][] = 'L\'URL "'.$route.'" est déjà utilisée.';
        }

        if (isset($_POST['errors'][0]) === false) {
            $plugin->settings->route = $_POST['plugin_view_route'];
            $plugin->update_settings($overwrite = true);

            $_POST['successes'][] = 'Champ "URL de l\'onglet" enregistré.';
        }
    }
}

if (isset($_POST['successes']) === true) {
    $MENUS->public = SimpleMenu::get_public_menus();
}
