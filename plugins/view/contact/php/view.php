<?php

use UniversiteRennes2\Isou\Plugin;

if (count($MENUS->public) > 1) {
    $TITLE .= ' - Contact';
}

$plugin = Plugin::get_record(array('codename' => 'contact', 'type' => 'view'));

$smarty->assign('message', $plugin->settings->message);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/contact/html');
$TEMPLATE = 'view.tpl';
