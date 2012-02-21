<?php

/*
   script vérifiant que le serveur dispose de tous les modules nécessaires au bon fonctionnement d'ISOU
*/

$extensions = array();
$extensions['pdo_sqlite'] = extension_loaded('pdo_sqlite');
$extensions['Phar'] = extension_loaded('Phar');
$extensions['curl'] = extension_loaded('curl');
$extensions['openssl'] = extension_loaded('openssl');
$extensions['dom'] = extension_loaded('dom');

echo "\nVérification de la configuration du serveur\n";

$error = FALSE;
foreach($extensions as $extension => $ext){
	if($ext === TRUE){
		echo $extension.niceDot($extension)." \033[0;32mok\033[0m\n";
	}else{
		echo $extension.niceDot($extension)." \033[0;31merreur\033[0m";
		$error = TRUE;
	}
}

echo "\n\n";

if($error === TRUE){
	echo "\033[0;31m!!\033[0m Un ou plusieurs modules PHP nécessaires au bon fonctionnement d'ISOU ne sont pas installés. \033[0;31m!!\033[0m\n";
	exit();
}

?>
