<?php

require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/classes/helpers/simple_menu.php';

$TITLE .= ' - Administration des services';

if(!isset($PAGE_NAME[1])){
	$PAGE_NAME[1] = '';
}

switch($PAGE_NAME[1]){
	case 'nagios-statusdat':
	case 'shinken-thruk':
		require PRIVATE_PATH.'/php/services/backend_index.php';
		break;
	default:
		require PRIVATE_PATH.'/php/services/isou_index.php';
}

$services_menu = array();
$services_menu['isou'] = new Isou\Helpers\SimpleMenu('Services ISOU', 'Afficher la liste des services ISOU', URL.'/index.php/services/isou');
$services_menu['nagios-statusdat'] = new Isou\Helpers\SimpleMenu('Services Nagios (Status.dat)', 'Afficher la liste des services Nagios (Status.dat)', URL.'/index.php/services/nagios-statusdat');
$services_menu['shinken-thruk'] = new Isou\Helpers\SimpleMenu('Services Shinken (Thruk)', 'Afficher la liste des services Shinken (Thruk)', URL.'/index.php/services/shinken-thruk');

if(isset($services_menu[$PAGE_NAME[1]])){
	$services_menu[$PAGE_NAME[1]]->selected = TRUE;
}else{
	$services_menu['isou']->selected = TRUE;
}

$smarty->assign('services_menu', $services_menu);

$smarty->assign('SUBTEMPLATE', $SUBTEMPLATE);

$TEMPLATE = 'services/services.tpl';

