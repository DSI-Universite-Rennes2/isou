<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

error_reporting(0);

if(defined('STDIN')){
	// SCRIPT CALL BY CLI
	$pwd = dirname(__FILE__).'/LOCK_UPDATE';
	if(is_file($pwd)){
		echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
		echo "Le fichier ".$pwd." a été détecté. Une mise à jour est déjà en cours.\n";
		exit(1);
	}else{
		touch($pwd);
	}

	$pwd = dirname(__FILE__).'/../../public';

	echo "\033[0;31mIMPORTANT : mettez à jour l'application avec votre utilisateur web (apache, www-data ou autre)\033[0m\nVoulez-vous continuer ? (y/n)\n";
	$owner = trim(fgets(STDIN));
	if(strtolower($owner) === 'n'){
		echo "\033[0;31mMerci de relancer l'installation avec le bon utilisateur.\033[0m\n";
		exit(0);
	}

	$file = $pwd.'/functions.php';
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

	require BASE.'/upgrade/version.php';
	require BASE.'/php/common_database.php';

	// load CFG
	$sql = "SELECT key, value FROM configuration WHERE key='version'";
	if($query = $db->query($sql)){
		$query = $query->fetch(PDO::FETCH_OBJ);
		$CFG = array();
		$CFG['version'] = $query->value;
	}
}else{
	// web call

	// Maximum execution time of each script, in seconds
	ini_set('max_execution_time', '600');

	// Maximum amount of memory a script may consume
	ini_set('memory_limit', '200M');

	ob_start();

	echo "Mise à jour de la version ".$CFG['version']." à la version ".CURRENT_VERSION."\n";

	touch(BASE.'/upgrade/LOCK_UPDATE');
}

require BASE.'/upgrade/functions.php';

/*
 * CREE UN BACKUP
 */
if(isset($CFG['auto_backup']) && $CFG['auto_backup'] === '1'){
	$display = "\nBackup de la précédente installation";
	try{
		$backup_dir = BASE.'/backup/';
		if(!is_dir(BASE.'/backup/')){
			if(mkdir(BASE.'/backup/') === FALSE){
				echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
				echo "Erreur retournée : impossible de créer le répertoire 'backup' dans ".BASE."/backup/\n";
				echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
				exit(1);
			}
		}
		echo $display.niceDot($display);
		$backup = new isouPharData($backup_dir.'backup_'.strftime('%Y%m%d_%H-%M').'.tar');
		$backup->addDir($pwd, 'public');
		$backup->addDir(BASE, 'private', array('private/backup', 'private/log'));

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

if(is_file(BASE.'/cron/LOCK_CRON')){
	echo "\nEn attente de la fin d'execution du cron...";
	while(is_file(BASE.'/cron/LOCK_CRON')){
		echo '...';
		sleep(3);
	}
	echo "\n\n";
}

// mise à jour du numéro de version dans le fichier config.php
if(strlen($CFG['version']) < 12){
	// update old old old version
	require BASE.'/upgrade/scripts/update_0.9.6.php';
	$CFG['version'] = '2012-02-16.1';
}

if($CFG['version'] === '2012-02-16.1'){
	// insertion de la variable 'local_mail'
	$sql = "INSERT INTO configuration(key, value) VALUES (?,?)";
	$query = $db->prepare($sql);
	$query->execute(array('local_mail', ''));

	// insertion de la variable 'auto_backup'
	$sql = "INSERT INTO configuration(key, value) VALUES (?,?)";
	$query = $db->prepare($sql);
	$query->execute(array('auto_backup', '1'));

	require BASE.'/upgrade/scripts/update_2012-02-16.1.php';
	$CFG['version'] = '2012-02-16.2';
}

if(defined('STDIN')){
	unlink(BASE.'/upgrade/LOCK_UPDATE');

	$sql = "UPDATE configuration SET value=? WHERE key='version'";
	$version = $db->prepare($sql);
	$version->execute(array(CURRENT_VERSION));

	echo "\033[0;32mMise à jour terminée !\033[0m\n\n";
}else{
	$file = LOG_PATH.'/update_'.CURRENT_VERSION.'.log';
	$i = 1;
	while(is_file($file)){
		$file = LOG_PATH.'/update_'.CURRENT_VERSION.'_'.$i.'.log';
		$i++;
	}
	$buffer = str_replace(array("\033[0;31m", "\033[0;32m", "\033[0m"), "", ob_get_flush());
	file_put_contents($file, $buffer);

	// avertissement par mail de la procédure aux administrateurs
	foreach($CFG['admin_mails'] as $mail){
		if(filter_var($mail, FILTER_VALIDATE_EMAIL) !== FALSE){
			isoumail($mail, 'Rapport de mise à jour '.NAME, $buffer);
		}
	}
}

// close pdo connection
$db = null;

?>
