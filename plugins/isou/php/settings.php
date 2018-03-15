<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/isou/html');

$options_yes_no = array(
1 => 'yes',
0 => 'no',
);

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$PLUGIN_TEMPLATE = 'settings.tpl';
