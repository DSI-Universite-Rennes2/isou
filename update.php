#!/usr/bin/php
<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

error_reporting(0);
define('SOURCE', dirname(__FILE__));

require SOURCE.'/install/functions.php';
require SOURCE.'/version.php';

$owner = trim(readline("\033[0;31mIMPORTANT : mettez à jour l'application avec votre utilisateur web (apache, www-data ou autre)\033[0m\nVoulez-vous continuer ? (y/n)\n"));
if(strtolower($owner) === 'n'){
	echo "\033[0;31mMerci de relancer l'installation avec le bon utilisateur.\033[0m\n";
	exit(0);
}

/*
 * COPIE DES FICHIERS ET REPERTOIRES 'PUBLICS'
 */
$config = trim(readline("Indiquez le chemin du fichier config.php\n".
						"exemple : \033[1;30m/var/www/config.php\033[0m\n"));

if(is_file($config)){
	// $public_path = strstr($config, '/config.php', TRUE);
	// contourne l'option TRUE ajoutée en php 5.3
	$public_path = explode('/config.php', $config);
	$public_path = $public_path[0];
	$pwd = $public_path;
	require $pwd.'/functions.php';
	require $config;
	$private_path = BASE;
}else{
	echo "Le fichier config.php n'a pas été trouvé à l'adresse ".$config.".\n";
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	exit(1);
}

/*
 * CREE UN BACKUP
 */
$display = "\nBackup de la précédente installation";
try{
	$backup_dir = SOURCE.'/backup/';
	if(!is_dir(SOURCE.'/backup/')){
		if(mkdir(SOURCE.'/backup/') === FALSE){
			echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
			echo "Erreur retournée : impossible de créer le répertoire 'backup' dans ".SOURCE."/backup/\n";
			echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
			exit(1);
		}
	}
	$backup = new PharData($backup_dir.'backup_'.strftime('%Y%m%d_%H-%M').'.tar.gz');
	$backup->buildFromDirectory($public_path);
	$backup->buildFromDirectory($private_path);
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n\n";
}catch (UnexpectedValueException $e){
	echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	echo "Erreur retournée : ".$e->getMessage()."\n";
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	exit(1);
}catch (BadMethodCallException $e){
	echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	echo "Erreur retournée : ".$e->getMessage()."\n";
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	exit(1);
}

$update_git = FALSE;
if(is_file(SOURCE.'/UPDATE_GIT_FLAG')){
	$update_git = TRUE;
}else{
	if(is_dir(SOURCE.'/.git')){
		$update_git = trim(readline("\nVoulez-vous que l'installateur fusionne votre version avec la version officielle ? (y/n)\n"));
		if(strtolower($update_git) === 'y'){
			$update_git = TRUE;
		}
	}
}

if($update_git === TRUE){
	$display = "\nÉcriture du témoin de mise à jour par git";
	if(touch(SOURCE.'/UPDATE_GIT_FLAG')){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mÉchec de la mise à jour. Merci vérifier les droits d'écriture sur ".SOURCE.", puis de relancer la procédure de mise à jour.\033[0m\n";
		exit(1);
	}

	$files = array();
	$files[] = 'css';
	$files[] = 'images';
	$files[] = 'js';
	$files[] = 'config.menu.php';
	$files[] = 'functions.php';
	$files[] = 'index.php';
	$files[] = 'rss.php';
	$files[] = 'rss.xsl';

	foreach($files as $file){
		$display = "Copie de ".$public_path."/".$file." vers ".SOURCE."/sources/".$file;
		if(cp($public_path.'/'.$file, SOURCE.'/sources/'.$file)){
			echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
		}else{
			echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
			echo "\033[0;31mÉchec de la mise à jour. Merci de relancer une installation complète.\033[0m\n";
			exit(1);
		}
	}

	$files = array();
	$files[] = 'classes';
	$files[] = 'cron';
	$files[] = 'html';
	$files[] = 'php';

	foreach($files as $file){
		$display = "Copie de ".$private_path."/".$file." vers ".SOURCE."/sources/".$file;
		if(cp($private_path.'/'.$file, SOURCE.'/sources/'.$file)){
			echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
		}else{
			echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
			echo "\033[0;31mÉchec de la mise à jour. Merci de relancer une installation complète.\033[0m\n";
			exit(1);
		}
	}

	if(is_file(SOURCE.'/sources/cron/LOCK_CRON')){
		// éventuellement, si le LOCK_CRON est définit, ne pas le recopier !
		unlink(SOURCE.'/sources/cron/LOCK_CRON');
	}

	$shell = shell_exec("cd '".SOURCE."' && git pull");
	$exp_shell = explode("\n", $shell);
	$conflicts = preg_grep('#^(Aborting|CONFLICT)#', $exp_shell);
	echo "\nSortie shell:\033[0;35m\n".$shell."\033[0m\n";
	if(count($conflicts) > 0){
		echo "\033[0;31mÉchec de la mise à jour. Merci de corriger les conflits, puis de relancer la mise à jour.\033[0m\n";
		exit(1);
	}else{
		$update_git = trim(readline("La fusion entre les deux versions semble s'être passée correctement. Voulez-vous continuer ? (y/n)\n"));
		if(strtolower($update_git) !== 'y'){
			echo "\033[0;31mMerci de corriger les conflits, puis de relancer la mise à jour.\033[0m\n";
			exit(0);
		}else{
			$update_git = TRUE;
		}
	}
}

$files = array();

if($update_git === TRUE){
	$files[] = 'css';
	$files[] = 'images';
	$files[] = 'config.menu.php';
}

$files[] = 'js';
$files[] = 'functions.php';
$files[] = 'index.php';
$files[] = 'rss.php';
$files[] = 'rss.xsl';

echo "\n";
foreach($files as $file){
	$display = "Copie de ".SOURCE."/sources/".$file." vers ".$public_path."/".$file;
	if(cp(SOURCE.'/sources/'.$file, $public_path.'/'.$file)){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mÉchec de la mise à jour. Merci de relancer une installation complète.\033[0m\n";
		exit(1);
	}
}

$files = array();
$files[] = 'classes';
$files[] = 'cron';
// $files[] = 'database';
$files[] = 'html';
$files[] = 'php';

foreach($files as $file){
	$display = "Copie de ".SOURCE."/sources/".$file." vers ".$private_path."/".$file;
	if(cp(SOURCE.'/sources/'.$file, $private_path.'/'.$file)){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mÉchec de la mise à jour. Merci de relancer une installation complète.\033[0m\n";
		exit(1);
	}
}

if(is_file(SOURCE.'/UPDATE_GIT_FLAG')){
	unlink(SOURCE.'/UPDATE_GIT_FLAG');
}

// commit git en local
if($update_git === TRUE){
	$display = "\nCommit local des changements";
	if(shell_exec("cd '".SOURCE."' && git commit -a -m 'update du ".strftime('%c')."'")){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mÉchec du commit local. Merci de commiter manuellement les changements.\033[0m\n";
	}
}

echo "\n\033[0;32mLa mise à jour est terminée.\033[0m\n\n";

// mise à jour du numéro de version dans le fichier config.php
if(VERSION !== CURRENT_VERSION){ 
	$cfg = file_get_contents($config);
	$cfg = str_replace("define('VERSION', '".VERSION."');", "define('VERSION', '".CURRENT_VERSION."');", $cfg);
	file_put_contents($config, $cfg);
}

if(is_file(SOURCE.'/version_updates.php')){
	require SOURCE.'/version_updates.php';
}

// vérification d'une nouvelle version de fisou
if($update_git === TRUE){
	$xpi = preg_grep('#/xpi/#', $exp_shell);
	if(count($xpi) > 0){
		$xpi_fisou = preg_grep('#sources/xpi/fisou[\d\.]+\.xpi#', $exp_shell);
		if(count($xpi_fisou) > 0){
			preg_match('#fisou[\d\.]+\.xpi#', current($xpi_fisou), $xpi_fisou);
			if(count($xpi_fisou) > 0){
				$xpi_fisou = $xpi_fisou[0];
			}else{
				unset($xpi_fisou);
			}
		}else{
			unset($xpi_fisou);
		}

		// création d'un script bash pour automatiser la distribution de fisou
		$build_fisou = "#!/bin/bash\n\n".
						"cd ".SOURCE."/fisou && \\\n".
						"make && \\\n".
						"mkdir -p ".$public_path."/xpi && \\\n";
		if(isset($xpi_fisou)){
			$build_fisou .= "cp build/fisou.xpi ".$public_path."/xpi/".$xpi_fisou." && \\\n";
		}
		$build_fisou .= "cp build/update.rdf build/fisou.xpi ".SOURCE."/sources/xpi/index.php ".$public_path."/xpi/ && \\\n".
						"rm -r build/ && \\\n".
						"cd ..";

		echo "Une nouvelle version de \033[1;34mFisou\033[0m est disponible.";
		if(file_put_contents(SOURCE."/build_fisou.sh", $build_fisou) === FALSE){
			echo "\nLe script bash build_fisou.sh n'a pas pu être généré.\n".
				"Veuillez vous placer dans le répertoire ".SOURCE."/fisou et lancer le make, ".
				"puis copier les fichiers générés dans ".SOURCE."/fisou/build dans le répertoire ".$public_path."/xpi/, ainsi que le fichier ".SOURCE."/sources/xpi/index.php\n\n";
		}else{
			echo " Veuillez lancer le script build_fisou.sh\n";
		}
		echo "nb: si ce n'est fait, pensez à changer l'URL dans le fichier ".SOURCE."/fisou/clean.sh avant de lancer le script build_fisou.sh.\n\n";
	}
}

?>
