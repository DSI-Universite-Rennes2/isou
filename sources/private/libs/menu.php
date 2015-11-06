<?php

require_once PRIVATE_PATH.'/classes/helpers/menu.php';

function get_menu(){
	global $db;

	$menu = array();

	$sql = "SELECT idmenu, label, title, url, active, model, position".
			" FROM menu".
			" ORDER BY position";
	$query = $db->prepare($sql);
	$query->execute();

	$query->setFetchMode(PDO::FETCH_CLASS, 'Isou\Helpers\Menu');

	while($item = $query->fetch()){
		$item->selected = FALSE;
		$menu[$item->url] = $item;
	}

	return $menu;
}

function get_menu_sorted_by_url(){
	global $db;

	$menu = array();

	$sql = "SELECT url, label".
			" FROM menu".
			" ORDER BY position";
	$query = $db->prepare($sql);
	$query->execute();

	return $query->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE);
}

function get_active_menu(){
	global $db;

	$menu = array();

	$sql = "SELECT idmenu, label, title, url, active, model, position".
			" FROM menu".
			" WHERE active=1".
			" ORDER BY position";
	$query = $db->prepare($sql);
	$query->execute();

	$query->setFetchMode(PDO::FETCH_CLASS, 'Isou\Helpers\Menu');

	while($item = $query->fetch()){
		$item->selected = FALSE;
		$menu[$item->url] = $item;
	}

	return $menu;
}

function get_active_menu_sorted_by_url(){
	global $db;

	$menu = array();

	$sql = "SELECT url, label".
			" FROM menu".
			" WHERE active=1".
			" ORDER BY position";
	$query = $db->prepare($sql);
	$query->execute();

	return $query->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE);
}

function get_administration_menu(){
	global $db;

	$menu = array();

	$sql = "SELECT menu.idmenu, menu.label, menu.title, menu.url, menu.model, menu.position, submenu.idsubmenu, submenu.label AS submenu".
			" FROM administration_menu menu, administration_submenu submenu".
			" WHERE submenu.idsubmenu=menu.idsubmenu".
			" ORDER BY submenu.position, menu.position";
	$query = $db->prepare($sql);
	$query->execute();

	$query->setFetchMode(PDO::FETCH_CLASS, 'Isou\Helpers\Menu');

	while($item = $query->fetch()){
		$item->selected = FALSE;
		$menu[$item->url] = $item;
	}

	return $menu;
}


?>
