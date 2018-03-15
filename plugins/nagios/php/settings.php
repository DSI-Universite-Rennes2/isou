<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/nagios/html');

$options_yes_no = array(1 => 'yes', 0 => 'no');

if (isset($_POST['plugin_nagios_enable'], $options_yes_no[$_POST['plugin_nagios_enable']]) === true) {
    $plugin->active = $_POST['plugin_nagios_enable'];
    $plugin->save();
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$PLUGIN_TEMPLATE = 'settings.tpl';
