<?php

$TITLE = NAME.' - Configuration de l\'authentification';

$errors = array();
$errors['Fichier config.php'] = array();
$errors['Base de données'] = array();

// Vérification du fichier config.php.
$config = file_get_contents(PUBLIC_PATH.'/config.php');

if(preg_match('/define\(.NAME/', $config) === 1){
	$errors['Fichier config.php'] = 'La constante <code>NAME</code> est n\'est plus utilisée. Vous pouvez la supprimer du fichier config.php';
}

if(preg_match('/define\(.HEADER/', $config) === 1){
	$errors['Fichier config.php'] = 'La constante <code>HEADER</code> est n\'est plus utilisée. Vous pouvez la supprimer du fichier config.php';
}

if(preg_match('/define\(.STATUSDAT_URL/', $config) === 1){
	$errors['Fichier config.php'] = 'La constante <code>STATUSDAT_URL</code> est n\'est plus utilisée. Vous pouvez la supprimer du fichier config.php';
}

if(preg_match('/define\(.DB_STAT_PATH/', $config) === 1){
	$errors['Fichier config.php'] = 'La constante <code>DB_STAT_PATH</code> est n\'est plus utilisée. Vous pouvez la supprimer du fichier config.php';
}

// Vérification de la base de données.
// TODO.

$smarty->assign('errors', $errors);

$SUBTEMPLATE = 'configuration/diagnostics.tpl';

