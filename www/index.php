<?php

use UniversiteRennes2\Isou\Plugin;

session_name('isou');
session_start();

if(!is_file(__DIR__.'/../config.php')){
	echo 'L\'application ne semble pas être installée.'.
		' Merci d\'exécuter en ligne de commande le script install.php qui se trouve dans ./sources/private/upgrade.';
	exit(1);
}

require __DIR__.'/../config.php';

$smarty = new Smarty();
$smarty->setTemplateDir(PRIVATE_PATH.'/html/');
$smarty->setCompileDir(PRIVATE_PATH.'/cache/smarty/');

// load scripts
$SCRIPTS = array();

// load styles
$STYLES = array();

require PRIVATE_PATH.'/php/common/database.php';

require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

// Charge les plugins.
$plugins = get_plugins();

// set title
if (isset($CFG['site_name']) === true) {
    $TITLE = $CFG['site_name'];
} else {
    $TITLE = 'Isou';
}

if (has_new_version() === true) {
	// Display maintenance page.
	$TEMPLATE = 'public/update.tpl';

	$IS_ADMIN = false;
	$MENU = array();
	$STATES = array();
} else {
	// Display default pages.
	require PRIVATE_PATH.'/php/common/authentification.php';

	// load states
	require PRIVATE_PATH.'/libs/states.php';
	$STATES = get_states();

	// load menu
	require PRIVATE_PATH.'/libs/menu.php';

	$MENU = get_active_menu();
	if($IS_ADMIN === TRUE){
		$ADMINISTRATION_MENU = get_administration_menu();
	}

	// routing
	$PAGE_NAME = explode('/', get_page_name('index.php', TRUE));
	if(isset($MENU[$PAGE_NAME[0]])){
		$current_page = $MENU[$PAGE_NAME[0]];
	}elseif(isset($ADMINISTRATION_MENU['Générale'][$PAGE_NAME[0]])){
		$current_page = $ADMINISTRATION_MENU['Générale'][$PAGE_NAME[0]];
	}elseif(isset($ADMINISTRATION_MENU['Avancée'][$PAGE_NAME[0]])){
		$current_page = $ADMINISTRATION_MENU['Avancée'][$PAGE_NAME[0]];
	}else{
		if(isset($CFG['default_menu'], $MENU[$CFG['default_menu']])){
			$current_page = $MENU[$CFG['default_menu']];
		}else{
			$current_page = current($MENU);
		}
	}
	$current_page->selected = TRUE;

	// load announcement
	if(isset($MENU[$current_page->url])){
		require PRIVATE_PATH.'/libs/announcements.php';

		$ANNOUNCEMENT = get_visible_announcement();
	}

	require PRIVATE_PATH.$current_page->model;
}

if (isset($CFG['theme']) === false || is_file(PUBLIC_PATH.'/themes/'.$CFG['theme'].'/theme.php') === false) {
	$CFG['theme'] = 'bootstrap';
}

require PUBLIC_PATH.'/themes/'.$CFG['theme'].'/theme.php';

$smarty->assign('TITLE', $TITLE);
$smarty->assign('SCRIPTS', $SCRIPTS);
$smarty->assign('STYLES', $STYLES);
$smarty->assign('IS_ADMIN', $IS_ADMIN);
$smarty->assign('CFG', $CFG);
$smarty->assign('STATES', $STATES);
$smarty->assign('MENU', $MENU);

if(isset($ANNOUNCEMENT) && $ANNOUNCEMENT !== FALSE){
	$smarty->assign('ANNOUNCEMENT', $ANNOUNCEMENT);
}

if(isset($ADMINISTRATION_MENU)){
	$smarty->assign('ADMINISTRATION_MENU', $ADMINISTRATION_MENU);
}

$smarty->display('common/html_head.tpl');
$smarty->display('common/html_body_header.tpl');
$smarty->display($TEMPLATE);
$smarty->display('common/html_body_footer.tpl');

// close pdo connection
$DB = null;

unset($_SESSION['messages']);
