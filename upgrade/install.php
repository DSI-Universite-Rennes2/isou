#!/usr/bin/php
<?php

// PHP CLI Colors – PHP Class Command Line Colors (bash)
// http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

error_reporting(0);
define('PRIVATE_PATH', __DIR__);
define('TIME', time());

require PRIVATE_PATH.'/functions.php';
require PRIVATE_PATH.'/version.php';

echo "\033[0;31mIMPORTANT : installez l'application avec votre utilisateur web (apache, www-data ou autre)\033[0m\nVoulez-vous continuer ? (y/n)\n";
$owner = trim(fread(STDIN, 2048));
if(strtolower($owner) === 'n'){
	echo "\033[0;31mMerci de relancer l'installation avec le bon utilisateur.\033[0m\n";
	exit(0);
}

$private_path = realpath(PRIVATE_PATH.'/../');
$public_path = realpath(PRIVATE_PATH.'/../../public/');

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
require PRIVATE_PATH.'/scripts/install_database.php';

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

echo "\nQuel est l'hôte de votre service CAS ?\n";
echo "exemple: \033[1;34mcas.example.com\033[0m\n";
$CAS_URL = trim(fgets(STDIN));
$CAS_URL = str_replace('http://', '', $CAS_URL);
$CAS_URL = str_replace('https://', '', $CAS_URL);

$CAS_URI = strstr($CAS_URL, '/');
if(substr($CAS_URI, 0, 1) === '/'){
	$CAS_URI = substr($CAS_URI, 1);
}

if(substr($CAS_URI, -1) === '/'){
	$CAS_URI = substr($CAS_URI, 0, -1);
}

$slash = strpos($CAS_URL, '/');
if($slash !== FALSE){
	$CAS_URL = substr($CAS_URL, 0, strpos($CAS_URL, '/'));
}

echo "\nQuelle est le port de votre service CAS ?\n";
echo "Défaut: \033[1;34m443\033[0m\n";
$CAS_PORT = intval(fgets(STDIN));
if(empty($CAS_PORT)){
	$CAS_PORT = 443;
}

echo "\nQuelles sont les comptes CAS autorisés à administrer l'application (séparés par des virgules) ?\n";
echo "Défaut: \033[1;34mvide\033[0m\n";
echo "exemple : \033[1;30mdurand_m, dupont_t\033[0m\n";
$ADMIN_USERS = explode(',', trim(fgets(STDIN)));
if(!is_array($ADMIN_USERS)){
	$ADMIN_USERS = array();
}

echo "\nQuelles sont les adresses mails devant recevoir des alertes mails (séparés par des virgules) ?\n";
echo "Défaut: \033[1;34mvide\033[0m\n";
echo "exemple : \033[1;30mexample1@example.com, example2@example.com\033[0m\n";
$ADMIN_MAILS = explode(',', trim(fgets(STDIN)));
if(!is_array($ADMIN_MAILS)){
	$ADMIN_MAILS = array();
}

echo "\nQuelles sont les plages IP de votre réseau universitaire (bornes d'IP séparée par une virgule, plages d'IP séparées par un point-virgule) ?\n";
echo "Défaut: \033[1;34m127.0.0.0,255.255.255.255\033[0m\n";
echo "exemple : \033[1;30m170.3.20.0,170.3.30.255;192.168.30.10,192.168.30.20\033[0m\n";
$IP_INTERNE = trim(fgets(STDIN));
if(empty($IP_INTERNE)){
	$IP_INTERNE = array(array('127.0.0.0', '255.255.255.255'));
}else{
	$IP_INTERNE = explode(';', $IP_INTERNE);
	foreach($IP_INTERNE as $key => $ip){
		$IP_INTERNE[$key] = explode(',', $ip);
	}
}

echo "\nQuelles sont les plages IP de votre service ?\n";
echo "Défaut: \033[1;34mvide\033[0m\n";
echo "exemple : \033[1;30m170.3.20.0,170.3.30.255;192.168.30.10,192.168.30.20\033[0m\n";
$IP_CRI = trim(fgets(STDIN));
if(empty($IP_CRI)){
	$IP_CRI = array();
}else{
	$IP_CRI = explode(';', $IP_CRI);
	foreach($IP_CRI as $key => $ip){
		$IP_CRI[$key] = explode(',', $ip);
	}
}

echo "\nQuelle tolérance souhaitez-vous appliquer lorsque Nagios détecte une panne (permet de ne pas recenser les faux-positifs) ?\n";
echo "Défaut: \033[1;34m5\033[0m\n";
$TOLERANCE = trim(fgets(STDIN));
$TOLERANCE = intval($TOLERANCE)*60;
if(empty($TOLERANCE)){
	$TOLERANCE = 5*60;
}

$error = TRUE;
while($error === TRUE){
	echo "\nSouhaitez-vous avec un expéditeur spécifique pour les alertes mails de l'application ?\n";
	echo "Défaut: \033[1;34mvide\033[0m\n";
	echo "exemple : \033[1;30misou@example.com\033[0m\n";
	$LOCAL_MAIL = trim(fgets(STDIN));

	if(!empty($LOCAL_MAIL) && !filter_var($LOCAL_MAIL, FILTER_VALIDATE_EMAIL)){
		echo "\n\033[0;31mL'adresse mail saisie n'est pas valide.\033[0m\n";
	}else{
		$error = FALSE;
	}
}

echo "\nSouhaitez-vous activer les sauvegardes automatiques lors des mises à jour de l'application (ATTENTION : peut considérablement ralentir le processus de mise à jour) ?\n";
echo "Défaut: \033[1;34mOui\033[0m\n";
$AUTO_BACKUP = trim(fgets(STDIN));

if(!empty($AUTO_BACKUP) && in_array($AUTO_BACKUP, array('N', 'n', 0))){
	$AUTO_BACKUP = 0;
}else{
	$AUTO_BACKUP = 1;
}


$config = array();
// since 0.9.6
$config['tolerance'] = $TOLERANCE;
$config['ip_local'] = json_encode($IP_INTERNE);
$config['ip_service'] = json_encode($IP_CRI);
$config['admin_users'] = json_encode($ADMIN_USERS);
$config['admin_mails'] = json_encode($ADMIN_MAILS);
$config['version'] = CURRENT_VERSION;
$config['last_update'] = TIME;
$config['last_check_update'] = TIME;
$config['last_cron_update'] = 0;
$config['last_daily_cron_update'] = 0;
$config['daily_cron_hour'] = '06:00';
$config['last_weekly_cron_update'] = TIME;
$config['last_yearly_cron_update'] = TIME;
$config['local_password'] = '';
// since 20120216.1
$config['auto_backup'] = $AUTO_BACKUP;
$config['local_mail'] = $LOCAL_MAIL;

echo "Paramètrage de la table 'configuration'\n";
foreach($config as $key => $value){
	$sql = "INSERT INTO configuration(key, value) VALUES(?, ?)";
	$query = $db2->prepare($sql);
	$display = "	Insertion de la clé \"".$key."\" dans la table configuration";
	if($query->execute(array($key, $value)) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}
}

echo "\n";


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

// nom du service, notamment utilisé dans l'onglet du navigateur
define('NAME', '".addslashes($NAME)."');

// titre utilisé en entête sur toutes les pages
define('HEADER', '".addslashes($HEADER)."');

// chemin d'installation de l'application
define('PRIVATE_PATH', '".$private_path."');

// chemin ou url du fichier status.dat de Nagios
define('STATUSDAT_URL', '".addslashes($STATUSDAT_URL)."');

// hôte de CAS sans protocole http://
define('CAS_URL', '".$CAS_URL."');

// URI de CAS
// ex: si votre hôte CAS est de type auth.example.com/CAS, saisir 'CAS'
define('CAS_URI', '".$CAS_URI."');

// port de CAS
define('CAS_PORT', ".$CAS_PORT.");

// version de CAS (hardcoded)
// define('CAS_VERSION', '2.0');

/* * * * * * * * * * * * * * * * *
 *  CONSTANTES A NE PAS MODIFIER *
 * * * * * * * * * * * * * * * * */

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
define('LOG_PATH', PRIVATE_PATH.'/log/');

// connecteur pdo de la base de données
define('DB_PATH', 'sqlite:'.PRIVATE_PATH.'/database/isou.sqlite3');

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
	echo "\033[0m\033[0;31mIMPORTANT :\033[0m\033[1;30m n'oubliez pas de lire le fichier ".PRIVATE_PATH."/README-CRONTAB pour configurer vos crons\033[0m\n\n";
}else{
	echo "Fichier config.php :\n";
	echo "--------------------\n";
	echo $config;
	echo "\n\nLe fichier config.php n'a pas pu être enregistré dans ".$public_path."/config.php\n";
}

?>
