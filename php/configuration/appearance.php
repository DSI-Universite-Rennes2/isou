<?php

$TITLE = NAME.' - Configuration de l\'apparence';

$menus = get_menu_sorted_by_url();
$menus_active = array_keys(get_active_menu_sorted_by_url());

$themes = array();
if($handle = opendir(PUBLIC_PATH.'/styles')){
	while(($entry = readdir($handle)) !== FALSE){
		if(ctype_alnum($entry) && is_dir(PUBLIC_PATH.'/styles/'.$entry)){
			$themes[$entry] = $entry;
		}
	}
	closedir($handle);
}

foreach(array('site_name', 'site_header', 'tolerance', 'menu_default', 'theme') as $key){
	if(isset($_POST[$key]) && $_POST[$key] !== $CFG[$key]){
		$value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
		if(set_configuration($key, $value)){
			$CFG[$key] = $value;
		}
	}
}

if(isset($_POST['menus_active']) && is_array($_POST['menus_active'])){
	$sql = "UPDATE menu SET active=0";
	$query = $DB->prepare($sql);
	$query->execute();

	$active_menus = array();

	foreach($_POST['menus_active'] as $menu_active){
		$sql = "UPDATE menu SET active=1 WHERE url=?";
		$query = $DB->prepare($sql);
		$query->execute(array($menu_active));

		$menus_active[] = $menu_active;
	}

	$MENU = get_active_menu();
}

$smarty->assign('menus', $menus);
$smarty->assign('themes', $themes);
$smarty->assign('menus_active', $menus_active);

$SUBTEMPLATE = 'configuration/appearance.tpl';

