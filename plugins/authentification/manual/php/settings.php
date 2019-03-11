<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/authentification/manual/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['plugin_manual_enable'], $options_yes_no[$_POST['plugin_manual_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_manual_enable']) {
        // Vérifie qu'il reste au moins une méthode d'authentification activée.
        if ($_POST['plugin_manual_enable'] === '0') {
            $count_authentification_method = 0;
            foreach ($modules as $module) {
                if ($module->active === '1' && $module->codename !== $plugin->codename) {
                    $count_authentification_method++;
                }
            }

            if ($count_authentification_method === 0) {
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

$AUTHENTIFICATION_TEMPLATE = 'settings.tpl';
