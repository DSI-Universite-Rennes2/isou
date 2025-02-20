<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/authentication/manual/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['plugin_manual_enable'], $options_yes_no[$_POST['plugin_manual_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_manual_enable']) {
        // Vérifie qu'il reste au moins une méthode d'authentification activée.
        if ($_POST['plugin_manual_enable'] === '0') {
            $count_authentication_method = 0;
            foreach ($modules as $module) {
                if ($module->active === '1' && $module->codename !== $plugin->codename) {
                    $count_authentication_method++;
                }
            }

            if ($count_authentication_method === 0) {
                $_POST['errors'][] = 'Il n\'est pas possible de désactiver cette méthode d\'authentification car c\'est la dernière méthode d\'authentification activée.';
            }
        }

        if (isset($_POST['errors'][0]) === false) {
            $plugin->active = $_POST['plugin_manual_enable'];
            $plugin->save();

            if ($plugin->active === '1') {
                $_POST['successes'][] = 'Authentification locale activée.';
            } else {
                $_POST['successes'][] = 'Authentification locale désactivée.';
            }
        }
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$AUTHENTICATION_TEMPLATE = 'settings.tpl';
