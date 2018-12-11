<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/shinken/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
    );

if (isset($_POST['plugin_shinken_enable'], $options_yes_no[$_POST['plugin_shinken_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_shinken_enable']) {
        $plugin->active = $_POST['plugin_shinken_enable'];
        $plugin->save();

        if ($plugin->active === '1') {
            $_POST['successes'][] = 'Plugin Shinken activé.';
        } else {
            $_POST['successes'][] = 'Plugin Shinken désactivé.';
        }
    }
}

if (isset($_POST['plugin_shinken_path']) === true) {
    if ($plugin->settings->thruk_path !== $_POST['plugin_shinken_path']) {
        $plugin->settings->thruk_path = $_POST['plugin_shinken_path'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'URL de Thruk enregistrée.';
    }
}

if (isset($_POST['plugin_shinken_username']) === true) {
    if ($plugin->settings->thruk_username !== $_POST['plugin_shinken_username']) {
        $plugin->settings->thruk_username = $_POST['plugin_shinken_username'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Utilisateur Thruk enregistré.';
    }
}

if (isset($_POST['plugin_shinken_password']) === true) {
    if ($plugin->settings->thruk_password !== $_POST['plugin_shinken_password'] && $_POST['plugin_shinken_password'] !== '*****') {
        $plugin->settings->thruk_password = $_POST['plugin_shinken_password'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Mot de passe Thruk enregistré.';
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$MONITORING_TEMPLATE = 'settings.tpl';
