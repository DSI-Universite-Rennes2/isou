<?php

if(!is_file(__DIR__.'/config.php')){
	echo 'L\'application ne semble pas être installée.'.
		' Merci d\'exécuter en ligne de commande le script install.php qui se trouve dans ./sources/private/upgrade.';
	exit(1);
}

require __DIR__.'/config.php';
require PRIVATE_PATH.'/classes/smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = PRIVATE_PATH.'/html/';
$smarty->compile_dir = PRIVATE_PATH.'/classes/smarty/compile/';

// set title
$TITLE = NAME;

// load scripts
require PRIVATE_PATH.'/classes/helpers/script.php';

$SCRIPTS = array();

// load styles
require PRIVATE_PATH.'/classes/helpers/style.php';

$STYLES = array();
$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/common.css');
$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/menu.css');

// parse url
$PAGE_NAME = get_page_name('index.php', true);

if($PAGE_NAME === 'rss'){
	header('Location: '.RSS_URL);
	exit();
}

require PRIVATE_PATH.'/upgrade/version.php';
require PRIVATE_PATH.'/php/common/database.php';

$sql = "SELECT key, value FROM configuration";
$CFG = array();
if($query = $db->query($sql)){
	while($config = $query->fetch(PDO::FETCH_OBJ)){
		if(in_array($config->key, array('ip_local', 'ip_service', 'admin_users', 'admin_mails'))){
			 $CFG[$config->key] = json_decode($config->value);
		}else{
			$CFG[$config->key] = $config->value;
		}
	}
}


require PRIVATE_PATH.'/php/common/authentification.php';

$sql = "SELECT idState, name, title, alt, src FROM states";
$flags = array();
if($query = $db->query($sql)){
	while($flag = $query->fetch(PDO::FETCH_OBJ)){
		$flags[] = $flag;
	}
}

if(isset($_GET['hide'])){
	$_SESSION['hide'] = intval($_GET['hide']);
}

if(isset($PAGE_NAME)){
	$PAGE_NAME = explode('/', $PAGE_NAME);
	$MAIN_PAGE_NAME = 0;
	if(isset($PAGE_NAME[0])){
		$MAIN_PAGE_NAME = $PAGE_NAME[0];
	}
	if(isset($MENU[$MAIN_PAGE_NAME])){
		if($MENU[$MAIN_PAGE_NAME]->public === TRUE || $IS_ADMIN){
			$model = PRIVATE_PATH.$MENU[$MAIN_PAGE_NAME]->model;
			$template = $MENU[$MAIN_PAGE_NAME]->template;
		}
	}
}

if(!isset($model)){
	$MAIN_PAGE_NAME = $DEFAULTMENU;
	$model = PRIVATE_PATH.$MENU[$DEFAULTMENU]->model;
	$template = $MENU[$DEFAULTMENU]->template;
}

if($MENU[$MAIN_PAGE_NAME]->public === TRUE){
	$sql = "SELECT message FROM annonce WHERE afficher = 1 AND message != ''";
	$annonce = '';
	if($annonce = $db->query($sql)){
		$annonce = $annonce->fetch();
		if(isset($annonce[0]) && !empty($annonce[0])){
			$annonce = '<p id="annonce">'.nl2br(stripslashes($annonce[0])).'</p>';
		}
	}
}

if($IS_ADMIN){
	if(!isset($_SESSION['hide'])){
		$_SESSION['hide'] = 1;
	}else{
		if(isset($_GET['hide'])){
			$_SESSION['hide'] = intval($_GET['hide']);
		}
	}

	if(isset($_GET['refresh'])){
		require PRIVATE_PATH.'/classes/isou/parser.function.php';
		require PRIVATE_PATH.'/classes/isou/update.functions.php';

		if(is_a(update_nagios_to_db(),'Exception')){
			$refresh = FALSE;
		}else{
			$refresh = TRUE;
		}
		$refresh_url = get_base_url('full', HTTPS);
	}else{
		(strpos(get_base_url('full', HTTPS), '?') === false)?$refresh_url = get_base_url('full', HTTPS).'?refresh=1':$refresh_url = get_base_url('full', HTTPS).'&amp;refresh=1';
	}
}else{
	$_SESSION['hide'] = 1;
}

if(count($_GET)>0){
	$connexion_url = get_base_url('full', HTTPS).'&amp;';
}else{
	$connexion_url = get_base_url('full', HTTPS).'?';
}

if($_SESSION['hide'] === 1){
	$TOLERANCE = $CFG['tolerance'];
}else{
	$TOLERANCE = 0;
}

if(CURRENT_VERSION === $CFG['version']){
	require $model;
}else{
	// maintenance page
	$template = 'public_update';
}

$smarty->assign('FULLURL', get_base_url('full', HTTPS));
$smarty->assign('TITLE', $TITLE);
$smarty->assign('SCRIPTS', $SCRIPTS);
$smarty->assign('STYLES', $STYLES);
$smarty->assign('page', $MAIN_PAGE_NAME);
$smarty->assign('is_admin', $IS_ADMIN);
$smarty->assign('flags', $flags);
$smarty->assign('menu', $MENU);
$smarty->assign('CFG', $CFG);
$smarty->assign('connexion_url', $connexion_url);
if(isset($annonce)){
	$smarty->assign('annonce', $annonce);
}
if(isset($refresh_url)){
	$smarty->assign('refresh_url',  $refresh_url);
}

$smarty->display('common/html_head.tpl');
$smarty->display('common/html_body_header.tpl');
$smarty->display($template.'.tpl');
$smarty->display('common/html_body_footer.tpl');

// close pdo connection
$db = null;

?>
