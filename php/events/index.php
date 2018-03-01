<?php

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/events_descriptions.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/classes/helpers/simple_menu.php';

$TITLE .= ' - Administration des évènements';

if(!isset($PAGE_NAME[1])){
	$PAGE_NAME[1] = 'prevus';
}

switch($PAGE_NAME[1]){
	case 'delete':
		require_once PRIVATE_PATH.'/php/events/delete.php';
		break;
	case 'edit':
		require_once PRIVATE_PATH.'/php/events/edit.php';
		break;
	case 'prevus':
	case 'imprevus':
	default:
		require_once PRIVATE_PATH.'/php/events/list.php';
}

?>
