<?php

require PRIVATE_PATH.'/php/views/settings.php';

if (isset($_POST['message']) === true) {
    $html_purifier = new HTMLPurifier();
    $plugin->settings->message = $html_purifier->purify($_POST['message']);

    $plugin->update_settings($overwrite = true);

    $_POST['successes'][] = 'Champ "Message" enregistré.';
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/contact/html');
$VIEW_TEMPLATE = 'settings.tpl';
