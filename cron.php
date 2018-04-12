<?php

use UniversiteRennes2\Isou\Plugin;

// Vérification que le script est bien exécuté en CLI.
if (defined('STDIN') === false) {
    die();
}

require __DIR__.'/config.php';

$starttime = microtime(true);
$LOGGER->addInfo('Début du cron '.strftime('%c'));

// Force la définition du niveau d'erreurs.
error_reporting(-1);
ini_set('error', LOGS_PATH.'/php_errors.log');
ini_set('display_errors', 'On');

require PRIVATE_PATH.'/php/common/database.php';

// Charge la configuration.
require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

// Vérifie si un nouvelle version n'est pas en cours d'installation.
if (has_new_version() === true) {
    $LOGGER->addInfo('Une nouvelle version est en cours d\'installation.');
    exit(0);
}

// Charge les plugins.
$plugins = get_plugins();

// Créé un fichier cron.pid.
$pid_file = PRIVATE_PATH.'/cron.pid';
if (is_file($pid_file) === true) {
    echo 'Le fichier '.$pid_file.' existe déjà. Un processus du cron est en cours.'.PHP_EOL;
    $pid = file_get_contents($pid_file);

    // un cron est déjà en cours d'execution
    $atime = fileatime($pid_file);
    if ($atime !== false && $atime + (10 * 60) < TIME) {
        // si le fichier existe depuis plus de 10 minutes, alerter les admins
        if (file_exists('/proc/'.$pid) === false) {
            unlink($pid_file);
            echo 'Aucun processus en cours n\'a pour identifiant '.$pid.'.'.PHP_EOL;
            echo 'Le fichier '.$pid_file.' a été supprimé.'.PHP_EOL;
        } else {
            error_log('Le fichier \''.$pid_file.'\' a été créé depuis plus de 10 minutes.'.PHP_EOL.
                'Il est probablement nécessaire de tuer le processus '.$pid.'.');
            exit(1);
        }
    }
}

file_put_contents($pid_file, getmypid());

require_once PRIVATE_PATH.'/libs/cron.php';

// Mets à jour les backends.
$plugins = Plugin::get_records(array('active' => true));
foreach ($plugins as $plugin) {
    if ($plugin->codename === 'isou') {
        continue;
    }

    $plugin_library_file = PRIVATE_PATH.'/plugins/'.$plugin->codename.'/lib.php';
    if (is_readable($plugin_library_file) === false) {
        $LOGGER->addWarning('Le fichier "'.$plugin_library_file.'" n\'existe pas.');
        continue;
    }

    require $plugin_library_file;
    $function_name = 'plugin_'.$plugin->codename.'_update';
    if (function_exists($function_name) === false) {
        $LOGGER->addWarning('La fonction "'.$function_name.'" n\'existe pas.');
        continue;
    }

    if ($function_name($plugin) === false) {
        $LOGGER->addWarning('La mise à jour du backend "'.$plugin->codename.'" ne s\'est pas passé correctement.');
    }
}

// Mets à jour les services ISOU.
update_services_tree();

// On regénère le fichier isou.json.
cron_regenerate_json();

// Nettoie les anciens évènements des plugins autres qu'Isou.
cron_delete_old_plugin_events();

// Mets à jour la base de données.
$sql = "UPDATE configuration SET value = :value WHERE key = :key";
$query = $DB->prepare($sql);
$query->execute(array(':value' => strftime('%FT%T'), ':key' => 'last_cron_update'));

// Notifications.
if ($CFG['notification_enabled'] === 1) {
    cron_notify();
}

unlink($pid_file);

$LOGGER->addInfo('Temps d\'exécution : '.(microtime(true) - $starttime).' secondes.');
$LOGGER->addInfo('Fin du cron '.strftime('%c'));
