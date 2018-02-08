<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Définis si la consultation des pages est forcée en HTTPS.
define('HTTPS', true);

// Définis si l'application est en mode debug (affiche les erreurs PHP à l'écran).
define('DEBUG', false);

// Définis si l'application est en mode developpement (désactive la phase d'authentification).
define('DEV', false);

// Définis le nom du service, notamment utilisé dans l'onglet du navigateur.
define('NAME', 'Isou');

// Définis le titre utilisé en entête sur toutes les pages.
define('HEADER', 'ISOU : État des services numériques offerts par l\'Université');

// Définis le nom de domaine du serveur CAS (sans protocole le https://).
define('CAS_URL', '');

// Définis l'URI du serveur CAS.
// Exemple: si votre hôte CAS est de type auth.example.com/CAS, saisir 'CAS'.
define('CAS_URI', '');

// Définis le port du serveur CAS.
define('CAS_PORT', 443);

// Définis la locale utilisée par l'application.
setlocale(LC_TIME, 'fr_FR.UTF8');
date_default_timezone_set('Europe/Paris');


/* * * * * * * * * * * * * * * * *
 *  CONSTANTES A NE PAS MODIFIER *
 * * * * * * * * * * * * * * * * */

// Définis les principaux répertoires de l'application.
define('PUBLIC_PATH', __DIR__.'/www');
define('PRIVATE_PATH', __DIR__);
define('CACHE_PATH', PRIVATE_PATH.'/cache');
define('LOGS_PATH', PRIVATE_PATH.'/logs');
define('DB_PATH', 'sqlite:'.PRIVATE_PATH.'/database/isou.sqlite3');

// Charge les fonctions et bibliothèques tierces.
require_once PRIVATE_PATH.'/common/functions.php';
require PRIVATE_PATH.'/vendor/autoload.php';

// Définis le logger.
$LOGGER = new Logger('isou');

if (defined('STDIN') === false) {
	$LOGGER->pushHandler(new StreamHandler(LOGS_PATH.'/isou.log'));
	$LOGGER->pushHandler(new StreamHandler(LOGS_PATH.'/error.log', Logger::ERROR));
}

// Calcule le timestamp de l'heure courante.
define('TIME', time());
define('STR_TIME', strftime('%Y-%m-%dT%H:%M', TIME));

// Calcule l'URL du service.
define('URL', get_base_url('dirname', HTTPS));

// Calcul l'URL du flux RSS.
define('RSS_URL', URL.'/rss.php');

// Calcul l'URL d'ISOU avec l'index.php.
define('ISOU_URL', URL.'/index.php');

// Définis le niveau de rapport d'erreurs de PHP.
error_reporting(-1);
ini_set('error', LOGS_PATH.'/php_errors.log');

if (DEBUG === true) {
	ini_set('display_errors', 'On');
}else{
	ini_set('display_errors', 'Off');
}

// Définis le numero de version de l'application.
define('VERSION', '0.9.5');
