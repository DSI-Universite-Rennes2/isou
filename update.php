#!/usr/bin/php
<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

error_reporting(0);
define('SOURCE', dirname(__FILE__));

require SOURCE.'/install/functions.php';

$owner = readline("\033[0;31mIMPORTANT : mettez à jour l'application avec votre utilisateur web (apache, www-data ou autre)\033[0m\nVoulez-vous continuer ? (y/n)\n");
if(strtolower($owner) === 'n'){
	echo "\033[0;31mMerci de relancer l'installation avec le bon utilisateur.\033[0m\n";
	exit(0);
}

/*
 * COPIE DES FICHIERS ET REPERTOIRES 'PUBLICS'
 */
$config = readline("Indiquez le chemin du fichier config.php\n".
						"exemple : \033[1;30m/var/www/config.php\033[0m\n");

if(is_file($config)){
	$public_path = strstr($config, '/config.php', TRUE);
	$pwd = $public_path;
	require $pwd.'/functions.php';
	require $config;
	$private_path = BASE;
}else{
	echo "Le fichier config.php n'a pas été trouvé à l'adresse ".$config.".\n";
	echo "\033[0;31mÉchec de la mise à jour\033[0m\n";
	exit(1);
}

$update_svn = FALSE;
if(is_file(SOURCE.'/UPDATE_SVN_FLAG')){
	$update_svn = TRUE;
}else{
	if(is_dir(SOURCE.'/.svn')){
		$update_svn = readline("\nVoulez-vous que l'installateur fusionne votre version avec la version officielle ? (y/n)\n");
		if(strtolower($update_svn) === 'y'){
			$update_svn = TRUE;
		}
	}
}

if($update_svn === TRUE){
	$display = "\nÉcriture du témoin de mise à jour par subversion";
	if(touch(SOURCE.'/UPDATE_SVN_FLAG')){
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

	$shell = shell_exec("cd '".SOURCE."' && svn update");
	$conflicts = explode("\n", $shell);
	$conflicts = preg_grep('#^C#', $conflicts);
	echo "Sortie shell:\n\033[0;35m\n".$shell."\033[0m\n";
	if(count($conflicts) > 0){
		echo "\033[0;31mÉchec de la mise à jour. Merci de corriger les conflits, puis de relancer la mise à jour.\033[0m\n";
		exit(1);
	}else{
		$update_svn = readline("La fusion entre les deux versions semblent s'être passée correctement. Voulez-vous continuer ? (y/n)\n");
		if(strtolower($update_svn) !== 'y'){
			echo "\033[0;31mMerci de corriger les conflits, puis de relancer la mise à jour.\033[0m\n";
			exit(0);
		}
	}
}

$files = array();

if($update_svn === TRUE){
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

if(is_file(SOURCE.'/UPDATE_SVN_FLAG')){
	unlink(SOURCE.'/UPDATE_SVN_FLAG');
}

echo "\n\033[0;32mLa mise à jour est terminée.\033[0m\n\n";

?>
