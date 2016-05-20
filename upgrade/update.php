<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

error_reporting(0);

// SCRIPT CALL BY CLI
$pwd = __DIR__.'/LOCK_UPDATE';
if(is_file($pwd)){
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	echo "Le fichier ".$pwd." a été détecté. Une mise à jour est déjà en cours.\n";
	exit(1);
}else{
	touch($pwd);
}


echo "\033[0;31mIMPORTANT : mettez à jour l'application avec votre utilisateur web (apache, www-data ou autre)\033[0m\nVoulez-vous continuer ? (y/n)\n";
$owner = trim(fgets(STDIN));
if(strtolower($owner) === 'n'){
	unlink($pwd);
	echo "\033[0;31mMerci de relancer l'installation avec le bon utilisateur.\033[0m\n";
	exit(0);
}

echo "\nDans quel répertoire se trouve votre fichier config.php ?\n";
echo "Défaut: \033[1;34m".realpath(__DIR__.'/../../public')."\033[0m\n";
$pwd = trim(fgets(STDIN));

if(is_dir($pwd) === FALSE){
	$pwd = __DIR__.'/../../public';
}

$file = __DIR__.'/../common/functions.php';
if(is_file($file)){
	require $file;
}else{
	echo "Le fichier functions.php n'a pas été trouvé à l'adresse ".realpath($file).".\n";
	echo "Cela signifie que vous avez probablement modifié l'arborescence par défaut. Merci de lancer la mise à jour en chargeant directement une page d'Isou via votre navigateur.\n";
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	exit(1);
}

$file = $pwd.'/config.php';
if(is_file($file)){
	require $file;
}else{
	echo "Le fichier config.php n'a pas été trouvé à l'adresse ".realpath($file).".\n";
	echo "Cela signifie que vous avez probablement modifié l'arborescence par défaut. Merci de lancer la mise à jour en chargeant directement une page d'Isou via votre navigateur.\n";
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	exit(1);
}

if (!defined('PRIVATE_PATH')) {
	define('PRIVATE_PATH', BASE);
}

require PRIVATE_PATH.'/upgrade/version.php';
require PRIVATE_PATH.'/php/common/database.php';

// load CFG
$sql = "SELECT key, value FROM configuration WHERE key='version'";
if($query = $DB->query($sql)){
	$query = $query->fetch(PDO::FETCH_OBJ);
	$CFG = array();
	$CFG['version'] = $query->value;
}

if($CFG['version'] === CURRENT_VERSION){
	unlink(PRIVATE_PATH.'/upgrade/LOCK_UPDATE');
	echo "\033[0;32mIsou est déjà à jour !\033[0m\n\n";
	exit(0);
}

echo "\nMise à jour de la version ".$CFG['version']." à la version ".CURRENT_VERSION."\n";
// $old_version = $CFG['version'];

require PRIVATE_PATH.'/upgrade/functions.php';

/*
 * CREE UN BACKUP
 */
if(isset($CFG['auto_backup']) && $CFG['auto_backup'] === '1'){
	$display = "\nBackup de la précédente installation";
	try{
		$backup_dir = PRIVATE_PATH.'/backup/';
		if(!is_dir(PRIVATE_PATH.'/backup/')){
			if(mkdir(PRIVATE_PATH.'/backup/') === FALSE){
				echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
				echo "Erreur retournée : impossible de créer le répertoire 'backup' dans ".PRIVATE_PATH."/backup/\n";
				echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
				exit(1);
			}
		}
		echo $display.niceDot($display);
		$backup = new isouPharData($backup_dir.'backup_'.strftime('%Y%m%d_%H-%M').'.tar');
		$backup->addDir($pwd, 'public');
		$backup->addDir(PRIVATE_PATH, 'private', array('private/backup', 'private/log'));

		echo " \033[0;32mok\033[0m\n\n";
	}catch (UnexpectedValueException $e){
		echo " \033[0;31merreur\033[0m\n";
		echo "Erreur retournée : ".$e->getMessage()."\n";
		echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
		exit(1);
	}catch (BadMethodCallException $e){
		echo " \033[0;31merreur\033[0m\n";
		echo "Erreur retournée : ".$e->getMessage()."\n";
		echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
		exit(1);
	}
}

if(is_file(PRIVATE_PATH.'/cron/LOCK_CRON')){
	echo "\nEn attente de la fin d'execution du cron...";
	while(is_file(PRIVATE_PATH.'/cron/LOCK_CRON')){
		echo '...';
		sleep(3);
	}
	echo "\n\n";
}


// mise à jour du numéro de version dans le fichier config.php
if(strlen($CFG['version']) < 12){
	// update old old old version
	if($CFG['version'] != '0.9.6'){
		require PRIVATE_PATH.'/upgrade/scripts/update_0.9.6.php';
	}
	$intVersion = 1;
}else{
	$intVersion = intval(str_replace('.', '', str_replace('-', '', $CFG['version'])));
}

// mise à jour 2012.1
if($intVersion < 201202161){
	// insertion de la variable 'local_mail'
	$sql = "INSERT INTO configuration(key, value) VALUES (?,?)";
	$query = $DB->prepare($sql);
	$query->execute(array('local_mail', ''));

	// insertion de la variable 'auto_backup'
	$sql = "INSERT INTO configuration(key, value) VALUES (?,?)";
	$query = $DB->prepare($sql);
	$query->execute(array('auto_backup', '1'));

	require PRIVATE_PATH.'/upgrade/scripts/update_2012.1.php';
	$intVersion++;
}

// mise à jour 2013.1
if($intVersion < 201300001){
	require PRIVATE_PATH.'/upgrade/scripts/update_2013.1.php';
	$intVersion++;
}

unlink(PRIVATE_PATH.'/upgrade/LOCK_UPDATE');

$sql = "UPDATE configuration SET value=? WHERE key='version'";
$version = $DB->prepare($sql);
$version->execute(array(CURRENT_VERSION));

$sql = "UPDATE configuration SET value=? WHERE key='last_update'";
$version = $DB->prepare($sql);
$version->execute(array(TIME));

// URL n'est pas calculée en CLI
// echo "Le changelog est disponible à l'adresse suivante : ".URL."/index.php/configuration?type=changelog&version=".$old_version."\n\n";
echo "\n\033[0;32mMise à jour terminée !\033[0m\n\n";

// close pdo connection
$DB = null;

?>