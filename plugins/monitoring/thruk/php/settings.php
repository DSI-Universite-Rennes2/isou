<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/thruk/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['plugin_thruk_enable'], $options_yes_no[$_POST['plugin_thruk_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_thruk_enable']) {
        $plugin->active = $_POST['plugin_thruk_enable'];
        $plugin->save();

        if ($plugin->active === '1') {
            $_POST['successes'][] = 'Plugin Thruk activé.';
        } else {
            $_POST['successes'][] = 'Plugin Thruk désactivé.';
        }
    }
}

if (isset($_POST['plugin_thruk_path']) === true) {
    if ($plugin->settings->thruk_path !== $_POST['plugin_thruk_path']) {
        $plugin->settings->thruk_path = $_POST['plugin_thruk_path'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'URL de Thruk enregistrée.';
    }
}

if (isset($_POST['plugin_thruk_username']) === true) {
    if ($plugin->settings->thruk_username !== $_POST['plugin_thruk_username']) {
        $plugin->settings->thruk_username = $_POST['plugin_thruk_username'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Utilisateur Thruk enregistré.';
    }
}

if (isset($_POST['plugin_thruk_password']) === true) {
    if ($plugin->settings->thruk_password !== $_POST['plugin_thruk_password'] && $_POST['plugin_thruk_password'] !== '* * * * *') {
        $plugin->settings->thruk_password = $_POST['plugin_thruk_password'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Mot de passe Thruk enregistré.';
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$MONITORING_TEMPLATE = 'settings.tpl';
