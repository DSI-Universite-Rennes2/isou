<?php

require_once PRIVATE_PATH.'/libs/services.php';

$smarty->assign('services', get_services(array('plugin' => PLUGIN_ISOU)));

$TEMPLATE = 'dependencies/services/list.tpl';
