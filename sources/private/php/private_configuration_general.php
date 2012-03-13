<?php

$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/configuration.css" media="screen" />';
$script = '<script type="text/javascript" src="'.URL.'/js/jquery-min.js"></script>'.
			'<script type="text/javascript" src="'.URL.'/js/jquery_configuration.js"></script>';
$title = NAME.' - Configuration générale';

if(!defined('HTMLPurifier')){
	require BASE.'/classes/htmlpurifier/library/HTMLPurifier.auto.php';
}
$HTMLPurifier = new HTMLPurifier();
$smarty->assign('HTMLPurifierVersion', $HTMLPurifier->version);

if(!defined('phpCAS')){
	require BASE.'/classes/phpCAS/CAS.php';
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
			$query = $db->prepare($sql);
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
				$query = $db->prepare($sql);
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
		$query = $db->prepare($sql);
		if($query->execute(array($_POST['localpassword'])) === FALSE){
			$_POST['error']['general']['error_localpassword'] = 'La clé "local_password" n\'a pas pu être mise à jour';
		}
	}else{
		$_POST['error']['general']['empty_fields'] = 'Tous les champs doivent être remplis';
	}
}

// traitement des liens "d'effacement"
if(isset($_GET['action'], $_GET['key'])){
	// reset cron
	if($_GET['action'] === 'reset'){
		if(in_array($_GET['key'], array('last_cron_update', 'last_daily_cron_update', 'last_weekly_cron_update', 'last_yearly_cron_update')) === TRUE){
			$_POST['error'][$_GET['key']] = array();
			$sql = "UPDATE configuration SET value=NULL WHERE key=?";
			$query = $db->prepare($sql);
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
				$query = $db->prepare($sql);
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

?>
