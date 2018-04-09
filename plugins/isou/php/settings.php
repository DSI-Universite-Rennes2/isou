<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/isou/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
    );

if (isset($_POST['plugin_isou_tolerance']) === true) {
    if ($plugin->settings->tolerance !== $_POST['plugin_isou_tolerance'] && ctype_digit($_POST['plugin_isou_tolerance']) === true) {
        if ($_POST['plugin_isou_tolerance'] > 10) {
            $_POST['plugin_isou_tolerance'] = 10;
        }

        $plugin->settings->tolerance = $_POST['plugin_isou_tolerance'] * 60;
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Tolérance d\'interruption enregistrée.';
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$PLUGIN_TEMPLATE = 'settings.tpl';
