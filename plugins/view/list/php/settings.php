<?php

require PRIVATE_PATH.'/php/views/settings.php';

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/list/html');
$VIEW_TEMPLATE = 'settings.tpl';
