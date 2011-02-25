#!/usr/bin/php
<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

// \033['.color.'m'.string.'\033[0m
// red = 0;31;

error_reporting(0);
define('BASE', dirname(__FILE__));

require BASE.'/install/functions.php';

/*
 * COPIE DES FICHIERS ET REPERTOIRES 'PUBLICS'
 */
echo "\033[0;34mCOPIE DES FICHIERS\033[0m\n";
$public_path = readline("Indiquer le répertoire d'installation de votre application\n".
						"exemple : \033[1;30m/var/www/\033[0m\n");

if(substr($public_path, -1) === '/'){
	$public_path = substr($public_path, 0, -1);
}

if(!is_dir($public_path)){
	if(empty($public_path) || !mkdir($public_path)){
		echo "La création du répertoire ".$public_path." a échoué.\n";
		echo "\033[0;31mÉchec de l'installation\033[0m\n";
		exit(1);
	}
}

$files = array();
$files[0] = 'css';
$files[1] = 'images';
$files[2] = 'js';
$files[3] = 'config.menu.php';
$files[4] = 'functions.php';
$files[5] = 'index.php';
$files[6] = 'rss.php';
$files[7] = 'rss.xsl';

echo "\n";
foreach($files as $file){
	$display = "Copie de ".BASE."/sources/".$file." vers ".$public_path."/".$file;
	if(cp(BASE.'/sources/'.$file, $public_path.'/'.$file)){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mÉchec de l'installation\033[0m\n";
		exit(1);
	}
}

/*
 * COPIE DES FICHIERS ET REPERTOIRES 'PRIVÉS'
 */
$private_path = readline("\nIndiquer un répertoire non accessible via votre serveur Web (\033[1;35moptionnel\033[0m)\n".
						"exemple : \033[1;30m/var/www-private/\033[0m\n".
						"Défaut: \033[1;34m".$public_path."\033[0m\n");
if(empty($private_path)){
	$private_path = $public_path;
}else{
	if(substr($private_path, -1) === '/'){
		$private_path = substr($private_path, 0, -1);
	}
	if(!is_dir($private_path)){
		if(!mkdir($private_path)){
			$private_path = $public_path;
		}else{
			echo "\n";
		}
	}else{
		echo "\n";
	}
}

$files = array();
$files[0] = 'classes';
$files[1] = 'cron';
$files[2] = 'database';
$files[3] = 'html';
$files[4] = 'php';

foreach($files as $file){
	$display = "Copie de ".BASE."/sources/".$file." vers ".$public_path."/".$file;
	if(cp(BASE.'/sources/'.$file, $private_path.'/'.$file)){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mÉchec de l'installation\033[0m\n";
		exit(1);
	}
}

/*
 * INSTALLATION BASE DE DONNÉES
 */
echo "\n\033[0;34mCRÉATION DE LA BASE DE DONNÉES\033[0m\n";
try{
	$db2 = new PDO('sqlite:'.$private_path.'/database/isou.sqlite3');
}catch(PDOException $e){
	echo "La création de la base de données a échoué (".$e->getMessage().")\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}
$display = "Création de la base de données 'isou.sqlite3'";
echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
require BASE.'/install/database.php';

try{
	$db1 = new PDO('sqlite:'.$private_path.'/database/isou-visits.sqlite3');
	$sql = 'CREATE TABLE visits(weeks TIMESTAMP, numOf TINYINT, os VARCHAR(32), browser VARCHAR(32), ip TINYINT, userAgent TEXT)';
	$db1->query($sql);
}catch(PDOException $e){
	echo "La création de la base de données a échoué (".$e->getMessage().")\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}
$display = "Création de la base de données 'isou-visits.sqlite3'";
echo $display.niceDot($display)." \033[0;32mok\033[0m\n";

/*
 * CREATION DU FICHIER CONFIG.PHP
 */
echo "\n\033[0;34mCRÉATION DU FICHIER DE CONFIGURATION\033[0m\n";
echo "\nVotre serveur utilise-t-il HTTPS ? (y/n)\n";
echo "Défaut: \033[1;34mn\033[0m\n";
$HTTPS = trim(fgets(STDIN));

if(strtolower($HTTPS) === 'y'){
	$HTTPS = 'TRUE';
}else{
	$HTTPS = 'FALSE';
}

echo "\nQuel nom souhaitez-vous donner à votre application ?\n";
echo "Défaut: \033[1;34mIsou\033[0m\n";
$NAME = trim(fgets(STDIN));
if(empty($NAME)){
	$NAME = 'Isou';
}else{
	echo "\n";
}

echo "\nQuel titre souhaitez-vous donner à votre application ?\n";
echo "Défaut: \033[1;34mISOU : État des services numériques offerts par l'Université\033[0m\n";
$HEADER = trim(fgets(STDIN));
if(empty($HEADER)){
	$HEADER = 'ISOU : État des services numériques offerts par l\'Université';
}else{
	echo "\n";
}

echo "\nQuelle est l'URL (ou le chemin si serveur commun) du fichier 'status.dat' de Nagios ?\n";
echo "Défaut: \033[1;34m/var/nagios/status.dat\033[0m\n";

$STATUSDAT_URL = trim(fgets(STDIN));
if(empty($STATUSDAT_URL)){
	$STATUSDAT_URL = '/var/nagios/status.dat';
}else{
	echo "\n";
}

echo "\nQuelle est l'URL de votre service CAS ?\n";
echo "exemple: \033[1;34mcas.example.com\033[0m\n";
$CAS_URL = trim(fgets(STDIN));
$CAS_URL = str_replace('http://', '', $CAS_URL);
$CAS_URL = str_replace('https://', '', $CAS_URL);

echo "\nQuelles sont les comptes CAS autorisés à administrer l'application (séparés par des virgules) ?\n";
echo "Défaut: \033[1;34mvide\033[0m\n";
echo "exemple : \033[1;30mdurand_m, dupont_t\033[0m\n";
$ADMIN_USERS = trim(fgets(STDIN));
if(empty($ADMIN_USERS)){
	$ADMIN_USERS = 'array()';
}else{
	$users = explode(',', $ADMIN_USERS);
	$ADMIN_USERS = '';
	foreach($users as $user){
		$ADMIN_USERS .= '\''.trim($user).'\',';
	}
	$ADMIN_USERS = 'array('.substr($ADMIN_USERS, 0, -1).')';
}

echo "\nQuelles sont les plages IP de votre réseau universitaire (bornes d'IP séparée par une virgule, plages d'IP séparées par un point-virgule) ?\n";
echo "Défaut: \033[1;34m127.0.0.0,255.255.255.255\033[0m\n";
echo "exemple : \033[1;30m170.3.20.0,170.3.30.255;192.168.30.10,192.168.30.20\033[0m\n";
$IP_INTERNE = trim(fgets(STDIN));
if(empty($IP_INTERNE)){
	$IP_INTERNE = 'array(array(\'127.0.0.0\', \'255.255.255.255\'))';
}else{
	$ranges = explode(';', $IP_INTERNE);
	$IP_INTERNE = '';
	foreach($ranges as $range){
		$ips = explode(',', $range);
		$IP_INTERNE .= 'array(';
		foreach($ips as $ip){
			$IP_INTERNE .= '\''.$ip.'\',';
		}
		$IP_INTERNE = substr($IP_INTERNE, 0, -1).'),';
	}
	$IP_INTERNE = 'array('.substr($IP_INTERNE, 0, -1).')';
	echo "\n";
}

echo "\nQuelles sont les plages IP de votre service ?\n";
echo "Défaut: \033[1;34mvide\033[0m\n";
echo "exemple : \033[1;30m170.3.20.0,170.3.30.255;192.168.30.10,192.168.30.20\033[0m\n";
$IP_CRI = trim(fgets(STDIN));
if(empty($IP_CRI)){
	$IP_CRI = 'array()';
}else{
	$ranges = explode(';', $IP_CRI);
	$IP_CRI = '';
	foreach($ranges as $range){
		$ips = explode(',', $range);
		$IP_CRI .= 'array(';
		foreach($ips as $ip){
			$IP_CRI .= '\''.$ip.'\',';
		}
		$IP_CRI = substr($IP_CRI, 0, -1).'),';
	}
	$IP_CRI = 'array('.substr($IP_CRI, 0, -1).')';
	echo "\n";
}

echo "\nQuelle tolérance souhaitez-vous appliquer lorsque Nagios détecte une panne (permet de ne pas recenser les faux-positifs) ?\n";
echo "Défaut: \033[1;34m5\033[0m\n";
$TOLERANCE = trim(fgets(STDIN));
$TOLERANCE = intval($TOLERANCE)*60;
if(empty($TOLERANCE)){
	$TOLERANCE = 5*60;
}

$config = "<?php

/* * * * * * * * * * * * * * * * *
 *     CONSTANTE MODIFIABLE      *
 * * * * * * * * * * * * * * * * */

// définit si le serveur est en HTTPS
define('HTTPS', ".$HTTPS.");

// définit si l'application est en mode debug
define('DEBUG', FALSE);

// définit si l'application est en mode developpement
define('DEV', FALSE);

// définit la tolérance aux faux-positifs de Nagios
// l'évènement ne sera pas affiché à l'utilisateur lambda si la différence entre ka date de début et la date de fin d'évènement est inférieure à TOLERANCE
define('TOLERANCE', ".$TOLERANCE.");

// tableau contenant des tableaux de plage d'IP faisant parties du réseau Interne
\$IP_INTERNE = ".$IP_INTERNE.";

// tableau contenant des tableaux de plage d'IP faisant parties du réseau des administrateurs d'ISOU
\$IP_CRI = ".$IP_CRI.";

// tableau contenant le login des administrateurs d'ISOU
\$ADMIN_USERS = ".$ADMIN_USERS.";

// nom du service, notamment utilisé dans l'onglet du navigateur
define('NAME', '".addslashes($NAME)."');

// titre utilisé en entête sur toutes les pages
define('HEADER', '".addslashes($HEADER)."');

// chemin d'installation de l'application
define('BASE', '".$private_path."');

// chemin ou url du fichier status.dat de Nagios
define('STATUSDAT_URL', '".addslashes($STATUSDAT_URL)."');

// URL de CAS sans protocole http://
define('CAS_URL', '".$CAS_URL."');

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
require \$pwd.'/config.menu.php';

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

?>";

if(file_put_contents($public_path.'/config.php', $config) !== FALSE){
	echo "\033[0;32mL'installation est terminée.\033[0m\n\n";
	echo "\033[1;30mVous pouvez éditer le fichier de configuration présent dans ".$public_path."/config.php\n";
	echo "Vous pouvez également éditer le fichier de configuration des menus présent dans ".$public_path."/config.menu.php\n\n";
	echo "\033[0m\033[0;31mIMPORTANT :\033[0m\033[1;30m n'oubliez pas de lire le fichier ".BASE."/README-CRONTAB pour configurer vos crons\033[0m\n\n";
}else{
	echo $config;
	echo "\n\nLe fichier config.php n'a pas pu être enregistré dans ".$public_path."/config.php\n";
}

?>
