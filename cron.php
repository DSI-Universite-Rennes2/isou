<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Plugin;

// Vérification que le script est bien exécuté en CLI.
if (defined('STDIN') === false) {
    die();
}

require __DIR__.'/config.php';

$starttime = microtime(true);
$LOGGER->info('Début du cron '.date('r'));

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
    $LOGGER->info('Une nouvelle version est en cours d\'installation.');
    exit(0);
}

// Charge les plugins.
$plugins = get_plugins();

// Crée un fichier cron.pid.
$pid_file = PRIVATE_PATH.'/cron.pid';
if (is_file($pid_file) === true) {
    echo 'Le fichier '.$pid_file.' existe déjà. Un processus du cron est en cours ?';
    $pid = file_get_contents($pid_file);

    if (ctype_digit($pid) === true && file_exists('/proc/'.$pid) === true) {
        // Le pid correspond à un processus en cours...
        echo ' Oui !'.PHP_EOL;

        // Si le fichier existe depuis plus de 10 minutes, alerter les admins.
        $atime = fileatime($pid_file);
        if ($atime !== false && ($atime + (10 * 60)) < TIME) {
            error_log('Le fichier \''.$pid_file.'\' a été créé depuis plus de 10 minutes.'.PHP_EOL.
                'Il est probablement nécessaire de tuer le processus '.$pid.'.');
            exit(1);
        }

        // On quitte ce processus pour ne pas interférer avec l'autre processus en cours.
        exit(0);
    } else {
        // Le pid ne correspond à aucun processus en cours...
        echo ' Non.'.PHP_EOL;

        unlink($pid_file);
        echo 'Aucun processus en cours n\'a pour identifiant '.$pid.'.'.PHP_EOL;
        echo 'Le fichier '.$pid_file.' a été supprimé.'.PHP_EOL;
    }
}

file_put_contents($pid_file, getmypid());

require_once PRIVATE_PATH.'/libs/cron.php';

// Met à jour les backends.
$plugins = Plugin::get_records(array('active' => true, 'type' => 'monitoring'));
foreach ($plugins as $plugin) {
    if ($plugin->codename === 'isou') {
        continue;
    }

    $plugin_library_file = PRIVATE_PATH.'/plugins/monitoring/'.$plugin->codename.'/lib.php';
    if (is_readable($plugin_library_file) === false) {
        $LOGGER->warning('Le fichier "'.$plugin_library_file.'" n\'existe pas.');
        continue;
    }

    require $plugin_library_file;
    $function_name = 'plugin_'.$plugin->codename.'_update';
    if (function_exists($function_name) === false) {
        $LOGGER->warning('La fonction "'.$function_name.'" n\'existe pas.');
        continue;
    }

    if ($function_name($plugin) === false) {
        $LOGGER->warning('La mise à jour du backend "'.$plugin->codename.'" ne s\'est pas passé correctement.');
    }
}

// Met à jour les services ISOU.
update_services_tree();

// On regénère le fichier isou.json.
if ($CFG['json_enabled'] === '1') {
    cron_regenerate_json();
}

// Nettoie les anciens évènements des plugins autres qu'Isou.
cron_delete_old_plugin_events();

// Met à jour la base de données.
$sql = "UPDATE configuration SET value = :value WHERE key = :key";
$query = $DB->prepare($sql);
$query->execute(array(':value' => date('Y-m-d\TH:i:s'), ':key' => 'last_cron_update'));

// Notifications.
if ($CFG['notifications_enabled'] === '1') {
    cron_notify();
}

// Report.
if ($CFG['report_enabled'] === '1') {
    cron_report();
}

// Recherche une mise à jour.
if ($CFG['check_updates_enabled'] === '1') {
    cron_check_updates();
}

// Collecte de statistiques.
if ($CFG['gather_statistics_enabled'] === '1') {
    cron_gather_statistics();
}

unlink($pid_file);

$LOGGER->info('Temps d\'exécution : '.(microtime(true) - $starttime).' secondes.');
$LOGGER->info('Fin du cron '.date('r'));
