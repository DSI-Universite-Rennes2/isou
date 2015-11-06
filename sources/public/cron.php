<?php

// vérification que le script est bien exécuté en CLI
if(!defined('STDIN')){
	die();
}

require __DIR__.'/config.php';

error_reporting(-1);

require PRIVATE_PATH.'/php/common_database.php';

// load CFG
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

// crée un fichier LOCK
if(is_file(PRIVATE_PATH.'/cron/LOCK_CRON')){
	if(is_file(PRIVATE_PATH.'/cron/LOCK_SPAM')){
		// don't spam ! :-)
		exit;
	}

	if(is_file(PRIVATE_PATH.'/cron/LOCK_WARNING')){
		$message = "Le fichier '".PRIVATE_PATH."/cron/LOCK_CRON' est toujours présent.\n\n".
					"Il doit s'agir d'une erreur de programmation.\n".
					"Merci de tuer le processus php associé à '".__DIR__."/cron.php',\npuis de supprimer les fichiers '".PRIVATE_PATH."/cron/LOCK_*'";
		touch(PRIVATE_PATH.'/cron/LOCK_SPAM');
		foreach($CFG['admin_mails'] as $mail){
			if(filter_var($mail, FILTER_VALIDATE_EMAIL) !== FALSE){
				isoumail($mail, 'ISOU: erreur de fichier LOCK', $message);
			}
		}
		exit;
	}

	// un cron est déjà en cours d'execution
	$atime = fileatime(PRIVATE_PATH.'/cron/LOCK_CRON');
	if($atime !== FALSE && $atime+(10*60) < TIME){
		// si le fichier existe depuis plus de 10 minutes, alerter les admins
		$message = "Le fichier '".PRIVATE_PATH."/cron/LOCK_CRON' a été créé depuis plus de 10 minutes\n\n".
					"Le fichier '".PRIVATE_PATH."/cron/LOCK_CRON' a été supprimé.";
		touch(PRIVATE_PATH.'/cron/LOCK_WARNING');
		touch(PRIVATE_PATH.'/cron/LOCK_CRON');
		foreach($CFG['admin_mails'] as $mail){
			if(filter_var($mail, FILTER_VALIDATE_EMAIL) !== FALSE){
				isoumail($mail, 'ISOU: erreur de fichier LOCK', $message);
			}
		}
	}else{
		exit;
	}
}else{
	if(is_file(PRIVATE_PATH.'/upgrade/LOCK_UPDATE')){
		// don't run cron during an update
		exit;
	}else{
		touch(PRIVATE_PATH.'/cron/LOCK_CRON');
	}
}

// cron servant à la synchro avec Nagios
require PRIVATE_PATH.'/classes/isou/update.functions.php';
require PRIVATE_PATH.'/classes/isou/parser.function.php';

// creation/modification du fichier de log
$log = update_nagios_to_db();

if($log instanceof Exception){
	add_log(LOG_FILE, 'ISOU', 'error', $log->getMessage());
}

$sql = "UPDATE configuration SET value=? WHERE key=?";
$query = $db->prepare($sql);
$query->execute(array(TIME, 'last_cron_update'));

$daily_cron_time = explode(':', $CFG['daily_cron_hour']);
$daily_cron_time = mktime($daily_cron_time[0], $daily_cron_time[1]);

if(strftime('%d', TIME) != strftime('%d', $CFG['last_daily_cron_update']) && TIME >= $daily_cron_time){
	// si on n'est pas le même jour que $CFG['last_daily_cron_update']
	require PRIVATE_PATH.'/cron/cron_daily.php';
	$sql = "UPDATE configuration SET value=? WHERE key=?";
	$query = $db->prepare($sql);
	$query->execute(array(TIME, 'last_daily_cron_update'));

	if(strftime('%Y', TIME) !== strftime('%Y', $CFG['last_yearly_cron_update'])){
		// on n'est pas à la même année que $CFG['last_yearly_cron_update']
		require PRIVATE_PATH.'/cron/cron_yearly.php';
		$sql = "UPDATE configuration SET value=? WHERE key=?";
		$query = $db->prepare($sql);
		$query->execute(array(TIME, 'last_yearly_cron_update'));
	}
}

error_reporting(0);
// génère le fichier isou.json
require PRIVATE_PATH.'/classes/isou/json.php';

// supprime les fichiers LOCK
if(is_file(PRIVATE_PATH.'/cron/LOCK_CRON')){
	unlink(PRIVATE_PATH.'/cron/LOCK_CRON');
}

if(is_file(PRIVATE_PATH.'/cron/LOCK_WARNING')){
	unlink(PRIVATE_PATH.'/cron/LOCK_WARNING');
}

?>
