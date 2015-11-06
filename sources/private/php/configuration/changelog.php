<?php

$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/configuration.css" media="screen" />';
$script = '';
$title = NAME.' - Changelog';

$_POST['error'] = array();
$newversion = array();

// vérification du formulaire général
if(isset($_GET['version'])){
	if(strlen($CFG['version']) < 12){
		$intVersion = 1;
	}else{
		$intVersion = intval(str_replace('.', '', str_replace('-', '', $CFG['version'])));
	}
	if($intVersion < 2012-02-16.1){
		if(isset($_POST['localmail'])){
			$_POST['error']['localmail'] = array();
			$_POST['localmail'] = trim($_POST['localmail']);
			if(!empty($_POST['localmail']) && !filter_var($_POST['localmail'], FILTER_VALIDATE_EMAIL)){
				$_POST['error']['localmail']['error_localmail'] = 'L\'adresse mail saisie n\'est pas valide';
			}else{
				$sql = "UPDATE configuration SET value=? WHERE key=?";
				$query = $db->prepare($sql);
				if($query->execute(array($_POST['localmail'], 'local_mail')) === FALSE){
					$_POST['error']['localmail']['error_localmail'] = 'La clé "local_mail" n\'a pas pu être insérée';
				}
			}
		}

		if(isset($_POST['autobackup'])){
			$_POST['error']['autobackup'] = array();
			$_POST['autobackup'] = intval($_POST['autobackup']);
			if($_POST['autobackup'] !== 1){
				$_POST['autobackup'] = 0;
			}
			$sql = "UPDATE configuration SET value=? WHERE key=?";
			$query = $db->prepare($sql);
			if($query->execute(array($_POST['autobackup'], 'auto_backup')) === FALSE){
				$_POST['error']['autobackup']['error_autobackup'] = 'La clé "auto_backup" n\'a pas pu être insérée';
			}
		}

		$newversion['201202161'] = TRUE;
		$CFG['version'] = '2012-02-16.1';
		$smarty->assign('autobackup', array('Non', 'Oui'));
	}

	if(count($newversion) === 0){
		if(is_file(BASE.'/upgrade/LOCK_UPDATE')){
			unlink(BASE.'/upgrade/LOCK_UPDATE');
		}
		if(is_file(BASE.'/upgrade/LOCK_CONFIG')){
			unlink(BASE.'/upgrade/LOCK_CONFIG');
		}

		$sql = "UPDATE configuration SET value=? WHERE key='version'";
		$version = $db->prepare($sql);
		$version->execute(array(CURRENT_VERSION));

		header('Location: '.URL.'/index.php/configuration?type=changelog#'.CURRENT_VERSION);
		exit();
	}

	if(count($_POST) > 2){
		$errors = 0;
		foreach($_POST['error'] as $error){
			$errors += count($error);
		}

		if($errors === 0){
			if(is_file(BASE.'/upgrade/LOCK_UPDATE')){
				unlink(BASE.'/upgrade/LOCK_UPDATE');
			}
			if(is_file(BASE.'/upgrade/LOCK_CONFIG')){
				unlink(BASE.'/upgrade/LOCK_CONFIG');
			}

			$sql = "UPDATE configuration SET value=? WHERE key='version'";
			$version = $db->prepare($sql);
			$version->execute(array(CURRENT_VERSION));

			$_POST['success'] = 'La mise à jour est maintenant terminée';
		}
	}
}

$smarty->assign('newversion', $newversion);

?>
