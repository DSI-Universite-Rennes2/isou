<?php

/* * * * * * * * * * * * * * * * *
 *     CONSTANTE MODIFIABLE      *
 * * * * * * * * * * * * * * * * */

// définit si le serveur est en HTTPS
define('HTTPS', FALSE);

// définit si l'application est en mode debug
define('DEBUG', FALSE);

// définit si l'application est en mode developpement
define('DEV', FALSE);

// définit la tolérance aux faux-positifs de Nagios
// l'évènement ne sera pas affiché à l'utilisateur lambda si la différence entre ka date de début et la date de fin d'évènement est inférieure à TOLERANCE
define('TOLERANCE', 300);

// tableau contenant des tableaux de plage d'IP faisant parties du réseau Interne
$IP_INTERNE = array(array('127.0.0.0', '255.255.255.255'));

// tableau contenant des tableaux de plage d'IP faisant parties du réseau des administrateurs d'ISOU
$IP_CRI = array();

// tableau contenant le login des administrateurs d'ISOU
$ADMIN_USERS = array();

// nom du service, notamment utilisé dans l'onglet du navigateur
define('NAME', 'Isou');

// titre utilisé en entête sur toutes les pages
define('HEADER', 'ISOU : État des services numériques offerts par l\'Université');

// chemin d'installation de l'application
define('BASE', '/var/www');

// chemin ou url du fichier status.dat de Nagios
define('STATUSDAT_URL', '/var/nagios/status.dat');

// URL de CAS sans protocole http://
define('CAS_URL', '');

// version de CAS (hardcoded)
// define('CAS_VERSION', '2.0');

/* * * * * * * * * * * * * * * * *
 *  CONSTANTES A NE PAS MODIFIER *
 * * * * * * * * * * * * * * * * */

// numero de version de l'application
define('VERSION', '0.9.0');

// locale utilisée par l'application
setlocale(LC_TIME, 'fr_FR.UTF8');

// URL du service
define('URL', get_base_url('dirname', HTTPS));

// timestamp de l'heure courante
define('TIME', time());

// inclut le fichier de configuration des menus
require $pwd.'/config.menu.php';

// URL du flux RSS
define('RSS_URL', URL.'/rss.php');

// URL d'ISOU avec l'index.php
define('ISOU_URL', URL.'/index.php');

// répertoire des logs
define('LOG_PATH', BASE.'/log/');

// format du nom de fichier des logs
define('LOG_FILE', LOG_PATH.'/'.strftime('%y-%m-%d',TIME).'.log');

// connecteur pdo de la base de données
define('DB_PATH', 'sqlite:'.BASE.'/database/isou.sqlite3');

// connecteur pdo de la base de données des statistiques de visite
define('DB_STAT_PATH', 'sqlite:'.BASE.'/database/isou-visits.sqlite3');

// définition du niveau de rapport d'erreur de PHP
if(DEV === TRUE || DEBUG === TRUE){
	error_reporting(-1);
}else{
	error_reporting(0);
}

?>
