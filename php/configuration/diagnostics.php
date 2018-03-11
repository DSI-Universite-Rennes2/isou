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

if(preg_match('/define\(.VERSION/', $config) === 1){
	$errors['Fichier config.php'][] = 'La constante <code>VERSION</code> est obsolète. Vous pouvez la supprimer du fichier config.php';
}

if (is_file(PUBLIC_PATH.'/config.php') === true) {
    $errors['Fichier config.php'][] = 'Le fichier <code>'.PUBLIC_PATH.'/config.php</code> n\'est plus utilisé. Vous pouvez le supprimer.';
}

// Vérification de la base de données.
$old_databases = array();

$db_path = dirname(substr(DB_PATH, strlen('sqlite:')));
if ($handle = opendir($db_path)) {
    while (($entry = readdir($handle)) !== false) {
        if ($entry[0] === '.') {
            continue;
        }

        if (is_file($db_path.'/'.$entry) === false) {
            continue;
        }

        if ($entry !== 'isou-visits.sqlite3' && preg_match('/^isou-[0-9]+\.sqlite3$/', $entry) !== 1) {
            continue;
        }

        $old_databases[] = $entry;
    }

    closedir($handle);
}

sort($old_databases);

foreach ($old_databases as $database) {
    $errors['Base de données'][] = 'Le fichier <code>'.$db_path.'/'.$database.'</code> est dorénavant obsolète. Il peut être supprimé.';
}

// Vérification du cron.
if (isset($CFG['last_cron_update']) === false || $CFG['last_cron_update'] === new DateTime('1970-01-01')) {
    $errors['Crons'][] = 'Le fichier cron.php ne semble pas être appelé régulièrement.';
} else if ((TIME - 10 * 60) > $CFG['last_cron_update']->getTimestamp()) {
    $errors['Crons'][] = 'Le fichier cron.php ne semble pas être appelé régulièrement.'.
        ' La dernière exécution du cron date du '.strftime('%c', $CFG['last_cron_update']->getTimestamp()).'.';
}

$smarty->assign('errors', $errors);

$SUBTEMPLATE = 'configuration/diagnostics.tpl';
