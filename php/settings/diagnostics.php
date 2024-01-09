<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$TITLE .= ' - Configuration de l\'authentification';

$errors = array();
$errors['Fichier config.php'] = array();
$errors['Base de données'] = array();
$errors['Crons'] = array();

// Vérification du fichier config.php.
$deprecated_constants = array();
$deprecated_constants[] = 'NAME';
$deprecated_constants[] = 'HEADER';
$deprecated_constants[] = 'CAS_URL';
$deprecated_constants[] = 'CAS_PORT';
$deprecated_constants[] = 'CAS_URI';
$deprecated_constants[] = 'STATUSDAT_URL';
$deprecated_constants[] = 'DB_STAT_PATH';
$deprecated_constants[] = 'VERSION';

foreach ($deprecated_constants as $deprecated_constant) {
    if (defined($deprecated_constant) === true) {
        $errors['Fichier config.php'][] = 'La constante <code>'.$deprecated_constant.'</code> est obsolète. Vous pouvez la supprimer du fichier config.php';
    }
}

if (is_file(PUBLIC_PATH.'/config.php') === true) {
    $errors['Fichier config.php'][] = 'Le fichier <code>'.PUBLIC_PATH.'/config.php</code> n\'est plus utilisé. Vous pouvez le supprimer.';
}

// Vérification de la base de données.
$old_databases = array();

$db_path = dirname(substr(DB_PATH, strlen('sqlite:')));

$handle = opendir($db_path);
if ($handle !== false) {
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
} elseif ($CFG['last_cron_update']->getTimestamp() < (TIME - 10 * 60)) {
    $errors['Crons'][] = 'Le fichier cron.php ne semble pas être appelé régulièrement.'.
        ' La dernière exécution du cron date du '.date('r', $CFG['last_cron_update']->getTimestamp()).'.';
}

$HTMLPurifier = new HTMLPurifier();
$smarty->assign('HTMLPurifierVersion', $HTMLPurifier->version);

$smarty->assign('phpCASVersion', phpCAS::getVersion());

$smarty->assign('errors', $errors);

$SUBTEMPLATE = 'settings/diagnostics.tpl';
