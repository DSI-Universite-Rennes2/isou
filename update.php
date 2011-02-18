#!/usr/bin/php
<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

// \033['.color.'m'.string.'\033[0m
// red = 0;31;

error_reporting(0);
define('SOURCE', dirname(__FILE__));

require SOURCE.'/install/functions.php';

/*
 * COPIE DES FICHIERS ET REPERTOIRES 'PUBLICS'
 */
$config = readline("Indiquer le chemin du fichier config.php\n".
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

/* * * *
Voulez-vous que l'installateur merge votre version avec la version officielle ?
cp isou SOURCE.'/sources'
shell_exec("svn update");
*/

$files = array();
// $files[0] = 'css';
// $files[1] = 'images';
$files[2] = 'js';
// $files[3] = 'config.menu.php';
$files[4] = 'functions.php';
$files[5] = 'index.php';
$files[6] = 'rss.php';
$files[7] = 'rss.xsl';

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
$files[0] = 'classes';
$files[1] = 'cron';
// $files[2] = 'database';
$files[3] = 'html';
$files[4] = 'php';

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

echo "\n\033[0;32mLa mise à jour est terminée.\033[0m\n\n";

?>
