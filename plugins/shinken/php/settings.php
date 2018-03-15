<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/shinken/html');

$options_yes_no = array(
1 => 'yes',
0 => 'no',
);

if (isset($_POST['plugin_shinken_enable'], $options_yes_no[$_POST['plugin_shinken_enable']]) === true) {
    $plugin->active = $_POST['plugin_shinken_enable'];
    $plugin->save();
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$PLUGIN_TEMPLATE = 'settings.tpl';
