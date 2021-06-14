<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Définit si la consultation des pages est forcée en HTTPS.
define('HTTPS', true);

// Définit si l'application est en mode debug (affiche les erreurs PHP à l'écran).
define('DEBUG', false);

// Définit si l'application est en mode developpement (désactive la phase d'authentification).
define('DEV', false);

// Définit la locale utilisée par l'application.
setlocale(LC_TIME, 'fr_FR.UTF8');
date_default_timezone_set('Europe/Paris');

/*
 * * * * * * * * * * * * * * * * * *
 *  CONSTANTES A NE PAS MODIFIER   *
 * * * * * * * * * * * * * * * * * *
 */

// Définit les principaux répertoires de l'application.
define('PUBLIC_PATH', __DIR__.'/www');
define('PRIVATE_PATH', __DIR__);
define('CACHE_PATH', PRIVATE_PATH.'/cache');
define('LOGS_PATH', PRIVATE_PATH.'/logs');
define('DB_PATH', 'sqlite:'.PRIVATE_PATH.'/database/isou.sqlite3');

// Définit les constantes contenant le chemin d'accès aux clés VAPID.
define('VAPID_PRIVATE_KEY', PRIVATE_PATH.'/database/vapid_private.key');
define('VAPID_PUBLIC_KEY', PRIVATE_PATH.'/database/vapid_public.key');

// Charge les fonctions et bibliothèques tierces.
require PRIVATE_PATH.'/common/functions.php';
require PRIVATE_PATH.'/vendor/autoload.php';
require PRIVATE_PATH.'/version.php';

// Définit le logger.
$LOGGER = new Logger('isou');

if (defined('STDIN') === false) {
    $LOGGER->pushHandler(new StreamHandler(LOGS_PATH.'/isou.log'));
    $LOGGER->pushHandler(new StreamHandler(LOGS_PATH.'/error.log', Logger::ERROR));
}

// Calcule le timestamp de l'heure courante.
define('TIME', time());
define('STR_TIME', strftime('%Y-%m-%dT%H:%M', TIME));

// Calcule l'URL du service.
define('URL', get_base_url($force_https = HTTPS));

// Calcule l'URL du flux RSS.
define('RSS_URL', URL.'/rss.php');

// Calcule l'URL d'ISOU avec l'index.php.
define('ISOU_URL', URL.'/index.php');

// Définit le niveau de rapport d'erreurs de PHP.
error_reporting(-1);
ini_set('error', LOGS_PATH.'/php_errors.log');

if (DEBUG === true) {
    ini_set('display_errors', 'On');
} else {
    ini_set('display_errors', 'Off');
}
