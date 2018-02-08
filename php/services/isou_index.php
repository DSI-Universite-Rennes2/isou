<?php

if(!isset($PAGE_NAME[2])){
	$PAGE_NAME[2] = '';
}

switch($PAGE_NAME[2]){
	case 'edit':
		require PRIVATE_PATH.'/php/services/isou_edit.php';
		break;
	case 'delete':
		require PRIVATE_PATH.'/php/services/isou_delete.php';
		break;
	case 'inspect':
		require PRIVATE_PATH.'/php/services/isou_inspect.php';
		break;
	default:
		require PRIVATE_PATH.'/php/services/isou_list.php';
}

