<?php

$backend = new StdClass();
$backend->url = $PAGE_NAME[1];

switch($PAGE_NAME[1]){
	case 'nagios-statusdat':
		$backend->type = UniversiteRennes2\Isou\Service::TYPE_NAGIOS_STATUSDAT;
		$backend->name = 'Nagios';
		$backend->fullname = 'Nagios (status.dat)';
		$backend->enabled = ($CFG['nagios_statusdat_enable'] === '1');
		break;
	case 'shinken-thruk':
		$backend->type = UniversiteRennes2\Isou\Service::TYPE_SHINKEN_THRUK;
		$backend->name = 'Shinken';
		$backend->fullname = 'Shinken (Thruk)';
		$backend->enabled = ($CFG['shinken_thruk_enable'] === '1');
		break;
}

if(!isset($PAGE_NAME[2])){
	$PAGE_NAME[2] = '';
}

switch($PAGE_NAME[2]){
	case 'edit':
		require PRIVATE_PATH.'/php/services/backend_edit.php';
		break;
	case 'delete':
		require PRIVATE_PATH.'/php/services/backend_delete.php';
		break;
	default:
		require PRIVATE_PATH.'/php/services/backend_list.php';
}

$smarty->assign('backend', $backend);


