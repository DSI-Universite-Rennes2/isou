<?php

$TITLE .= ' - Configuration de l\'authentification';

$errors = array();
$errors['Fichier config.php'] = array();
$errors['Base de données'] = array();
$errors['Crons'] = array();

// Vérification du fichier config.php.
$config = file_get_contents(PRIVATE_PATH.'/config.php');

if(preg_match('/define\(.NAME/', $config) === 1){
	$errors['Fichier config.php'][] = 'La constante <code>NAME</code> est obsolète. Vous pouvez la supprimer du fichier config.php';
}

if(preg_match('/define\(.HEADER/', $config) === 1){
	$errors['Fichier config.php'][] = 'La constante <code>HEADER</code> est obsolète. Vous pouvez la supprimer du fichier config.php';
}

if(preg_match('/define\(.STATUSDAT_URL/', $config) === 1){
	$errors['Fichier config.php'][] = 'La constante <code>STATUSDAT_URL</code> est obsolète. Vous pouvez la supprimer du fichier config.php';
}

if(preg_match('/define\(.DB_STAT_PATH/', $config) === 1){
	$errors['Fichier config.php'][] = 'La constante <code>DB_STAT_PATH</code> est obsolète. Vous pouvez la supprimer du fichier config.php';
}

if (is_file(PUBLIC_PATH.'/config.php') === true) {
    $errors['Fichier config.php'][] = 'Le fichier <code>'.PUBLIC_PATH.'/config.php</code> n\'est plus utilisé. Vous pouvez le supprimer.';
}

// Vérification de la base de données.
// TODO.

// Vérification du cron.
if (isset($CFG['last_cron_update']) === false || empty($CFG['last_cron_update']) === true) {
    $errors['Crons'][] = 'Le fichier cron.php ne semble pas être appelé régulièrement.';
} else if ((TIME - 10 * 60) > $CFG['last_cron_update']) {
    $errors['Crons'][] = 'La dernière exécution du cron date du '.strftime('%c', $CFG['last_cron_update']);
}

$smarty->assign('errors', $errors);

$SUBTEMPLATE = 'configuration/diagnostics.tpl';

