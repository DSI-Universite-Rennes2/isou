<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

// Contrôle que le script est bien exécuté par Composer.
if (getenv('COMPOSER_DEV_MODE') === false) {
    echo 'Le script `upgrade.php` ne peut plus être exécuté directement. Merci d\'utiliser la commande `composer install` pour mettre à jour Isou.'.PHP_EOL;
    exit(1);
}

// Positionne les liens symboliques pour TinyMCE.
$tinymce_directory = __DIR__.'/www/scripts/tinymce';
if (is_dir($tinymce_directory) === false) {
    if (mkdir($tinymce_directory, 0755, $recursive = true) === false) {
        echo 'Une erreur est survenue lors de la création du répertoire "'.$tinymce_directory.'"'.PHP_EOL;
        exit(1);
    }
}

$symlinks = array();
$symlinks['../../../vendor/tinymce/tinymce/tinymce.min.js'] = __DIR__.'/www/scripts/tinymce/tinymce.min.js';
$symlinks['../../../vendor/tinymce/tinymce/icons'] = __DIR__.'/www/scripts/tinymce/icons';
$symlinks['../../../vendor/tinymce/tinymce/models'] = __DIR__.'/www/scripts/tinymce/models';
$symlinks['../../../vendor/tinymce/tinymce/plugins'] = __DIR__.'/www/scripts/tinymce/plugins';
$symlinks['../../../vendor/tinymce/tinymce/skins'] = __DIR__.'/www/scripts/tinymce/skins';
$symlinks['../../../vendor/tinymce/tinymce/themes'] = __DIR__.'/www/scripts/tinymce/themes';
foreach ($symlinks as $target => $link) {
    if (file_exists($link) === true) {
        continue;
    }

    symlink($target, $link);
}

// Charge la configuration pour initialiser la procédure d'installation ou de mise à jour.
require __DIR__.'/config.php';
require PRIVATE_PATH.'/libs/upgrade.php';

$db_file_path = substr(DB_PATH, 7);

// Définit l'environnement qui doit être utilisé par Phinx.
$environment = getenv('ISOU_ENV');
if ($environment === false || in_array($environment, array('demo', 'tests'), $strict = true) === false) {
    $environment = 'production';
}

if ($environment !== 'production') {
    try {
        // Contrôle la cohérence entre le nom de l'environnement et le nom de la base de données.
        if (basename($db_file_path, '.sqlite3') !== $environment) {
            throw new Exception('Vous avez utilisé la variable d’environnement ISOU_ENV='.$environment.'.'.PHP_EOL.
                'Cependant, la constante DB_PATH dans votre fichier config.php ne pointe pas vers la base de données '.$environment.'.sqlite3.'.PHP_EOL);
        }

        // Demande confirmation avant de supprimer la base de données de démo.
        $delete_database = true;
        if ($environment === 'demo' && is_file($db_file_path) === true) {
            $db_lock_file_path = $db_file_path.'.lock';
            if (is_file($db_lock_file_path) === true && unlink($db_lock_file_path) === false) {
                throw new Exception('Impossible de supprimer le fichier '.$db_lock_file_path.'.'.PHP_EOL);
            }

            echo PHP_EOL;
            echo 'Souhaitez-vous réinstaller la base de données '.$db_file_path.' ? (o/n)'.PHP_EOL;
            $response = trim(fgets(STDIN));
            if (in_array($response, array('o', 'y', 'O', 'Y'), $strict = true) === false) {
                $delete_database = false;

                if (touch($db_lock_file_path) === false) {
                    throw new Exception('Impossible de créer le fichier '.$db_lock_file_path.'.'.PHP_EOL);
                }
            }
        }

        // Supprime la base de données à chaque exécution des environnements de démo ou de tests.
        if ($delete_database === true && is_file($db_file_path) === true && unlink($db_file_path) === false) {
            throw new Exception('Impossible de supprimer le fichier '.$db_file_path.'.'.PHP_EOL);
        }
    } catch (Exception $exception) {
        echo PHP_EOL;
        echo 'Échec lors de l’installation !'.PHP_EOL;
        echo PHP_EOL;
        echo $exception->getMessage().PHP_EOL;
        exit(1);
    }
}

if (is_file($db_file_path) === false) {
    // Installation.
    try {
        initialize_phinx($environment);
    } catch (Exception $exception) {
        echo 'Echec lors de l\'installation !'.PHP_EOL;
        echo $exception->getMessage().PHP_EOL;
        exit(1);
    }

    require PRIVATE_PATH.'/php/common/database.php';

    // Installe les plugins.
    echo PHP_EOL;
    upgrade_plugins();

    echo PHP_EOL;
    isou_update_version();

    echo PHP_EOL;
    echo 'Installation terminée.'.PHP_EOL;
    exit(0);
}

require PRIVATE_PATH.'/php/common/database.php';

// Charge la configuration.
require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

if (isset($CFG['version']) === false) {
    $CFG['version'] = '';
}

if (has_new_version() === false && upgrade_plugins($check_only = true) === false) {
    echo 'Aucune mise à jour à effectuer.'.PHP_EOL;
    exit(0);
}

// Avertissement avant la mise à jour.
echo PHP_EOL;
if (empty($CFG['version']) === true) {
    echo 'Vous vous apprêtez à mettre à jour votre instance Isou en version '.CURRENT_VERSION.'.'.PHP_EOL.PHP_EOL;
} else {
    echo 'Vous vous apprêtez à mettre à jour votre instance Isou de la version '.$CFG['version'].' à la version '.CURRENT_VERSION.'.'.PHP_EOL.PHP_EOL;
}
echo '   - ATTENTION ! Avant de lancer la procédure, il est fortement recommandé de faire une sauvegarde du fichier "'.$db_file_path.'"'.PHP_EOL.PHP_EOL;
echo 'Souhaitez-vous lancer la procédure de mise à jour en version '.CURRENT_VERSION.' ? (o/n)'.PHP_EOL;
$response = trim(fgets(STDIN));
if (in_array($response, array('o', 'y', 'O', 'Y'), true) === false) {
    exit(0);
}

echo PHP_EOL;

try {
    switch ($CFG['version']) {
        case '':
        case '0.9.0':
            upgrade_090_to_095($environment);
            // Pas de break. On enchaine sur l'upgrade de la version 0.9.0 à 0.9.5.
        case '0.9.5':
            upgrade_095_to_096($environment);
            // Pas de break. On enchaine sur l'upgrade de la version 0.9.5 à 0.9.6.
        case '0.9.6':
            upgrade_096_to_0100($environment);
            // Pas de break. On enchaine sur l'upgrade de la version 0.9.6 à 0.10.0.
        case '0.10.0':
        case '2012-02-16.1':
            upgrade_0100_to_0110($environment);
            // Pas de break. On enchaine sur l'upgrade de la version 0.10.0 à 0.11.0.
        case '0.11.0':
        case '2012-03-16.1':
            upgrade_0110_to_100($environment);
            // Pas de break. On enchaine sur l'upgrade de la version 0.11.0 à 1.0.0.
        case '1.0.0':
        case '2013-00-00.1':
            upgrade_100_to_200($environment);
            // Pas de break. On enchaine sur l'upgrade de la version 1.0.0 à 2.0.0.
        case '2.0.0':
        case '2.1.0':
        case '2.1.1':
        case '2.1.2':
            upgrade_200_to_300($environment);
            // Pas de break. On enchaine les mises à jour.
        case '3.0.0':
            upgrade_300_to_301($environment);
            // Pas de break. On enchaine les mises à jour.
        case '3.0.1':
            upgrade_301_to_302($environment);
            // Pas de break. On enchaine les mises à jour.
        case '3.0.2':
        case '3.0.3':
        case '3.0.4':
        case '3.1.0':
        case '3.1.1':
        case '3.1.2':
        case '3.2.0':
        case '3.2.1':
        case '3.2.2':
            upgrade_to_3_3_0($environment);
            // Pas de break. On enchaine les mises à jour.
        case '3.3.0':
        case '3.3.1':
            upgrade_to_4_0_0($environment);
            // Pas de break. On enchaine les mises à jour.
        case '4.0.0':
        case '4.0.1':
        case '4.1.0':
        case '4.1.1':
        case '4.1.2':
        case '4.1.3':
        case '4.1.4':
        case '4.1.5':
        case '4.1.6':
        case '4.1.7':
        case '4.2.0':
        case '4.2.1':
            upgrade_to_4_3_0($environment);
            // Pas de break. On enchaine les mises à jour.
        default:
            // Finally, upgrade plugins.
            echo PHP_EOL;
            upgrade_plugins();
    }
} catch (Exception $exception) {
    echo 'Echec lors de la mise à jour !'.PHP_EOL;
    echo $exception->getMessage().PHP_EOL;
    exit(1);
}

// Met à jour la date de dernière mise à jour et le numéro de version d'isou.
echo PHP_EOL;
isou_update_version();

echo PHP_EOL;
echo 'Mise à jour terminée.'.PHP_EOL;
