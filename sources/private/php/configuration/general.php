<?php

$TITLE = NAME.' - Configuration générale';

$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_configuration.js');

if(!defined('HTMLPurifier')){
	require PRIVATE_PATH.'/classes/htmlpurifier/library/HTMLPurifier.auto.php';
}
$HTMLPurifier = new HTMLPurifier();
$smarty->assign('HTMLPurifierVersion', $HTMLPurifier->version);

if(!class_exists('phpCAS')){
	require PRIVATE_PATH.'/classes/phpCAS/CAS.php';
}
$smarty->assign('phpCASVersion', phpCAS::getVersion());

$_POST['error'] = array();

// vérification du formulaire général
if(isset($_POST['generalsubmit'])){
	$_POST['error']['general'] = array();
	if(isset($_POST['tolerance'], $_POST['dailycronhour'], $_POST['localpassword'])){
		// tolerance
		$_POST['tolerance'] = intval($_POST['tolerance']);
		if($_POST['tolerance'] % 60 === 0){
			$sql = "UPDATE configuration SET value=? WHERE key='tolerance'";
			$query = $DB->prepare($sql);
			if($query->execute(array($_POST['tolerance'])) === FALSE){
				$_POST['error']['general']['error_tolerance'] = 'La clé "tolerance" n\'a pas pu être mise à jour';
			}
		}else{
			$_POST['error']['general']['error_tolerance'] = 'La clé "tolerance" doit être un multiple de 60';
		}

		// daily_cron_hour
		$_POST['dailycronhour'] = trim($_POST['dailycronhour']);
		if(preg_match('/^\d{1,2}:\d{2}$/', $_POST['dailycronhour']) === 0){
			$_POST['error']['general'][1] = 'Le champ "daily_cron_hour" doit être en format "HH:MM"';
		}else{
			$dailycronhour = explode(':', $_POST['dailycronhour']);

			if($dailycronhour[0] <= 23 && $dailycronhour[1] <= 59){
				$sql = "UPDATE configuration SET value=? WHERE key='daily_cron_hour'";
				$query = $DB->prepare($sql);
				if($query->execute(array($_POST['dailycronhour'])) === FALSE){
					$_POST['error']['general']['error_dailycronhour'] = 'La clé "daily_cron_hour" n\'a pas pu être mise à jour';
				}
			}else{
				$_POST['error']['general'][1] = 'Le champ "daily_cron_hour" doit être en format "HH:MM". L\'heure indiquée n\'est pas correcte';
			}
		}
		
		// local_password
		$_POST['localpassword'] = trim($_POST['localpassword']);
		$sql = "UPDATE configuration SET value=? WHERE key='local_password'";
		$query = $DB->prepare($sql);
		if($query->execute(array($_POST['localpassword'])) === FALSE){
			$_POST['error']['general']['error_localpassword'] = 'La clé "local_password" n\'a pas pu être mise à jour';
		}

		// theme
		if($_POST['theme'] !== $CFG['theme']){
			if(is_file(PUBLIC_PATH.'/styles/'.$_POST['theme'].'/theme.php')){
				$sql = "UPDATE configuration SET value=? WHERE key='theme'";
				$query = $DB->prepare($sql);
				if($query->execute(array($_POST['theme'])) === FALSE){
					$_POST['error']['general']['error_theme'] = 'La clé "theme" n\'a pas pu être mise à jour';
				}else{
					$CFG['theme'] = $_POST['theme'];
				}
			}else{
				$_POST['errors'][] = 'Le thème n\'est pas valide.';
			}
		}
	}else{
		$_POST['error']['general']['empty_fields'] = 'Tous les champs doivent être remplis';
	}
}

// vérification du formulaire menu
$menu = get_menu();
$active_menu_options = get_active_menu_sorted_by_url();
if(isset($_POST['menusubmit'])){
	$_POST['errors'] = array();
	$_POST['successes'] = array();
	if(isset($_POST['default_menu'])){
		$menu_options = array();

		// menu
		foreach($menu as $item){
			$menu_options[$item->url] = $item->label;

			$active = NULL;
			if(isset($_POST['menu'])){
				$found = in_array($item->url, $_POST['menu']);
			}else{
				$found = FALSE;
			}

			if($item->active === '0' && $found){
				$active = '1';
			}elseif($item->active === '1' && !$found){
				$active = '0';
			}

			if($active !== NULL){
				$item->active = $active;
				$results = $item->save();
				$_POST['errors'] = array_merge($_POST['errors'], $results['errors']);
				$_POST['successes'] = array_merge($_POST['successes'], $results['successes']);
				if($item->active === '1'){
					$MENU[$item->url] = clone($item);
					$active_menu_options[$item->url] = $item->label;
				}else{
					unset($MENU[$item->url]);
				}
			}
		}

		// set default menu page
		if(!isset($CFG['default_menu']) || $_POST['default_menu'] !== $CFG['default_menu']){
			if(isset($active_menu_options[$_POST['default_menu']])){
				$sql = "UPDATE configuration SET value=? WHERE key='default_menu'";
				$query = $DB->prepare($sql);
				if($query->execute(array($_POST['default_menu'])) === FALSE){
					$_POST['error']['menu']['error_menulocalpassword'] = 'La clé "local_password" n\'a pas pu être mise à jour';
				}else{
					$CFG['default_menu'] = $_POST['default_menu'];
				}
			}else{
				$_POST['errors'][] = 'Le menu par défaut choisi n\'est pas dans la liste des menus activés.';
			}
		}
	}
}else{
	$menu_options = array();
	foreach($menu as $item){
		$menu_options[$item->url] = $item->label;
	}
}
$smarty->assign('menu_options', $menu_options);
$smarty->assign('active_menu', array_keys($MENU));
$smarty->assign('active_menu_options', $active_menu_options);

// traitement des liens "d'effacement"
if(isset($_GET['action'], $_GET['key'])){
	// reset cron
	if($_GET['action'] === 'reset'){
		if(in_array($_GET['key'], array('last_cron_update', 'last_daily_cron_update', 'last_weekly_cron_update', 'last_yearly_cron_update')) === TRUE){
			$_POST['error'][$_GET['key']] = array();
			$sql = "UPDATE configuration SET value=NULL WHERE key=?";
			$query = $DB->prepare($sql);
			if($query->execute(array($_GET['key'])) === FALSE){
				$_POST['error'][$_GET['key']]['error_db'] = 'La clé "'.$_GET['key'].'" n\'a pas pu être mise à jour';
			}else{
				$CFG[$_GET['key']] = 0;
				$_POST['error'][$_GET['key']]['none'] = 'La clé "'.$_GET['key'].'" a été mise à jour';
			}
		}	
	}elseif($_GET['action'] === 'drop' && isset($_GET['index'])){
		if(in_array($_GET['key'], array('ip_local', 'ip_service', 'admin_users', 'admin_mails')) === TRUE){
			$_POST['error'][$_GET['key']] = array();
			if(isset($CFG[$_GET['key']][$_GET['index']-1])){
				unset($CFG[$_GET['key']][$_GET['index']-1]);
				// re-order index keys
				$CFG[$_GET['key']] = array_merge($CFG[$_GET['key']]);
				$sql = "UPDATE configuration SET value=? WHERE key=?";
				$query = $DB->prepare($sql);
				if($query->execute(array(json_encode($CFG[$_GET['key']]), $_GET['key'])) === FALSE){
					$_POST['error'][$_GET['key']]['error_db'] = 'La valeur n\'a pas pu être supprimée de la clé "'.$_GET['key'].'".';
				}else{
					$_POST['error'][$_GET['key']]['none'] = 'La valeur a été supprimée de la clé "'.$_GET['key'].'".';
				}
			}else{
				$_POST['error'][$_GET['key']]['bad_index'] = "L'index indiqué n'est pas valide";
			}
		}
	}
}

$smarty->assign('CFG', $CFG);
$smarty->assign('error', $_POST['error']);

$themes = array();
if($handle = opendir(PUBLIC_PATH.'/styles')){
	while(($entry = readdir($handle)) !== FALSE){
		if(ctype_alnum($entry)){ // test is_dir() ?
			$themes[$entry] = $entry;
		}
	}
	closedir($handle);
}
$smarty->assign('themes', $themes);


?>
