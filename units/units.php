<?php

/*
   CREATE TABLE dependencies ( idDependence INTEGER PRIMARY KEY, message TEXT, newStateForChild TINYINT, stateOfParent TINYINT, idService TINYINT, idServiceParent TINYINT);

	CREATE TABLE events ( idEvent INTEGER PRIMARY KEY, beginDate TIMESTAMP, endDate TIMESTAMP, typeEvent INTEGER);
	CREATE TABLE events_description( idEventDescription INTEGER PRIMARY KEY, description TEXT, autogen TINYINT);
	CREATE TABLE events_info ( idEventInfo INTEGER PRIMARY KEY, shortText TEXT, longText TEXT, idEvent INTEGER);
	CREATE TABLE events_isou ( idEventIsou INTEGER PRIMARY KEY, period INT, isScheduled TINYINT NOT NULL DEFAULT '1', idService TINYINT, idEventDescription TINYINT, idEvent TINYINT);
	CREATE TABLE events_nagios ( idEventNagios INTEGER PRIMARY KEY, state TINYINT, idService INTEGER, idEvent INTEGER);

	CREATE TABLE services (idService INTEGER PRIMARY KEY,name VARCHAR(64),nameForUsers VARCHAR(64),url VARCHAR(64), state TINYINT,comment VARCHAR(64),enable TINYINT,visible TINYINT,readonly TINYINT,rssKey TINYINT ,idCategory TINYINT);
*/

define('BASE', realpath(dirname(__FILE__).'/../sources/private'));
define('DB_PATH', 'sqlite:'.dirname(__FILE__).'/units.sqlite3');
define('UNITS', TRUE);
define('TIME', time());
define('LOG_LEVEL', 0);

$TOLERANCE = 0;
// 2 jours avant
$BEFORE = mktime(0,0,0)-(48*60*60);
// 2 jours apres
$AFTER = mktime(0,0,0)+(48*60*60);

if(is_file(substr(DB_PATH, 7))){
	unlink(substr(DB_PATH, 7));
}

$db2 = new PDO(DB_PATH, '', '');
$db = $db2;

require BASE.'/../public/functions.php';
require BASE.'/upgrade/functions.php';
echo "\nCREATION DE LA BASE POUR LES TESTS UNITAIRES\n".
	"------------------------------------------------\n\n";
require BASE.'/upgrade/scripts/install_database.php';
require BASE.'/classes/isou/update.functions.php';
require BASE.'/classes/isou/isou_event.class.php';
require BASE.'/classes/isou/isou_service.class.php';

define('STATE_GREEN', '0');
define('STATE_ORANGE', '1');
define('STATE_RED', '2');
define('STATE_BLUE', '3');

define('READONLY_ON', '1');
define('READONLY_OFF', '0');

define('VISIBLE_ON', '1');
define('VISIBLE_OFF', '0');

//categories
$sql = "INSERT INTO categories VALUES(1, ?, 1)";
$query = $db2->prepare($sql);
$query->execute(array('units test'));

// services
$sql = "INSERT INTO services VALUES(1, 'service_nagios_db_1', NULL, '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_OFF, READONLY_OFF));

$sql = "INSERT INTO services VALUES(2, 'service_nagios_web_1', NULL, '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$titi = $query->execute(array(STATE_ORANGE, VISIBLE_OFF, READONLY_OFF));

$sql = "INSERT INTO services VALUES(3, 'service_nagios_web_2', NULL, '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_OFF, READONLY_OFF));

$sql = "INSERT INTO services VALUES(4, 'Service final', 'service_isou_web_direct', '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_ON, READONLY_OFF));

$sql = "INSERT INTO services VALUES(5, 'Service final', 'service_isou_web_socle', '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_OFF, READONLY_OFF));

$sql = "INSERT INTO services VALUES(6, 'Service final', 'service_isou_web_socle_final_1', '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_ON, READONLY_OFF));

$sql = "INSERT INTO services VALUES(7, 'Service final', 'service_isou_web_socle_final_2', '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_ON, READONLY_OFF));

$sql = "INSERT INTO services VALUES(8, 'Service final', 'service_isou_readonly', '', ?, '', 1, ?, ?, NULL, 1)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_GREEN, VISIBLE_ON, READONLY_ON));

$sql = "SELECT state, readonly FROM services where idService=8";
$query = $db2->prepare($sql);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

// dependencies
// service_isou_web_direct (#4)
$sql = "INSERT INTO dependencies VALUES(1, 'Base de données stoppée', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_RED, STATE_ORANGE, 4, 1)); // state service isou, state service parent, id service isou, id service parent

$sql = "INSERT INTO dependencies VALUES(2, 'Service web n°1 stoppé', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 4, 2)); // state service isou, state service parent, id service isou, id service parent

$sql = "INSERT INTO dependencies VALUES(3, 'Service web n°2 stoppé', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 4, 3)); // state service isou, state service parent, id service isou, id service parent

// service_isou_web_socle (#5)
$sql = "INSERT INTO dependencies VALUES(4, 'Base de données stoppée', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 5, 1)); // state service isou, state service parent, id service isou, id service parent

$sql = "INSERT INTO dependencies VALUES(5, 'Service web n°1 stoppé', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 5, 2)); // state service isou, state service parent, id service isou, id service parent

// service_isou_web_socle_final_1 (#6)
$sql = "INSERT INTO dependencies VALUES(6, 'Service web socle stoppé', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 6, 5)); // state service isou, state service parent, id service isou, id service parent

// service_isou_web_socle_final_2 (#7)
$sql = "INSERT INTO dependencies VALUES(7, 'Service web socle stoppé', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 7, 5)); // state service isou, state service parent, id service isou, id service parent

$sql = "INSERT INTO dependencies VALUES(8, 'Base de données stoppée', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_RED, STATE_ORANGE, 7, 1)); // state service isou, state service parent, id service isou, id service parent

// service_isou_readonly (#8)
$sql = "INSERT INTO dependencies VALUES(9, 'Base de données stoppée', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 8, 1)); // state service isou, state service parent, id service isou, id service parent

$sql = "INSERT INTO dependencies VALUES(10, 'Service web n°1 stoppé', ?, ?, ?, ?)";
$query = $db2->prepare($sql);
$query->execute(array(STATE_ORANGE, STATE_ORANGE, 8, 2)); // state service isou, state service parent, id service isou, id service parent

update_nagios_to_db();

function get_parents($idChild){
	global $LOGGER;

	$parents = array();

	try {
		$dbr = new PDO(DB_PATH, '', '');
		$sql = "SELECT DISTINCT D.idServiceParent, S.name, S.nameForUsers, S.state".
		" FROM dependencies AS D, services AS S".
		" WHERE S.idService = D.idServiceParent".
		" AND D.idService = :0".
		" ORDER BY UPPER(S.name), UPPER(S.nameForUsers)";

		$services = $dbr->prepare($sql);
		$services->execute(array($idChild));
		while($service = $services->fetch(PDO::FETCH_OBJ)){
			$tmpParents = get_parents($service->idServiceParent);
			if(count($tmpParents) > 0){
				$service->parents = $tmpParents;
			}
			$parents[] = $service;
		}

	} catch (PDOException $e) {
		$LOGGER->addError($e->getMessage());
	}

	// close pdo connection
	$dbr = null;

	return $parents;
}

/*
   TESTS UNITAIRES
*/

define('OK', " \033[0;32mok\033[0m");
define('FAILED', " \033[0;31mÉchec\033[0m");

echo "\nTESTS UNITAIRES\n".
	"-------------------\n\n";

// Nombre d'évènements ISOU attendus
$display = "Nombre d'évènements ISOU attendus correct ?";
echo $display.niceDot($display);
$sql = "SELECT count(EI.idEventIsou) AS count FROM events_isou EI";
$query = $db->prepare($sql);
$query->execute();
$results = $query->fetch(PDO::FETCH_OBJ);
if($results->count === "4"){
	echo OK."\n";
}else{
	echo FAILED."\n";
}

// Nombre d'évènements ISOU attendus
$display = "Nombre de descriptions ISOU attendus correct ?";
echo $display.niceDot($display);
$sql = "SELECT count(ED.idEventDescription) AS count FROM events_description ED";
$query = $db->prepare($sql);
$query->execute();
$results = $query->fetch(PDO::FETCH_OBJ);
if($results->count === "3"){
	echo OK."\n";
}else{
	echo FAILED."\n";
}

// changement d'état lorsqu'un service à un service parent dans un état anormal
$display = "Changement d'état lorsqu'un service à un service parent dans un état anormal";
echo $display.niceDot($display);

$sql = "SELECT S.state FROM services S WHERE idService=6";
$query = $db->prepare($sql);
$query->execute();
$results = $query->fetch(PDO::FETCH_OBJ);
if($results->state !== '0'){
	echo OK."\n";
}else{
	echo FAILED."\n";
}

// Ne pas créer un évènement lorsqu'un service est forcé à 'vert'
$display = "Ne pas créer un évènement lorsqu'un service est forcé à 'vert'";
echo $display.niceDot($display);

$sql = "SELECT E.idEvent".
	" FROM services S, events E, events_isou EI".
	" WHERE EI.idEvent = E.idEvent".
	" AND E.typeEvent = 0".
	" AND S.idService = EI.idService".
	" AND S.idService = ?";
$query = $db->prepare($sql);
$query->execute(array(8));
$results = $query->fetchAll();
if(count($results) > 0){
	echo FAILED."\n";
}else{
	echo OK."\n";
}

// Redondance des serveurs (1 off, 1 on)
$display = "Gestion de la redondance des serveurs";
echo $display.niceDot($display);

$sql = "SELECT S.state FROM services S WHERE idService=4";
$query = $db->prepare($sql);
$query->execute();
$results = $query->fetch(PDO::FETCH_OBJ);
if($results->state === '0'){
	echo OK."\n";
}else{
	echo FAILED."\n";
}


// Descriptions cascadées
$display = "Description cascadée : 1 niveau";
echo $display.niceDot($display);
$sql = "SELECT ED.description FROM events_description ED, events_isou EI WHERE ED.idEventDescription=EI.idEventDescription AND EI.idService=4";
$query = $db->prepare($sql);
$query->execute();
if($results = $query->fetch(PDO::FETCH_OBJ)){
	if($results->description === 'Service web n°1 stoppé'){
		echo OK."\n";
	}else{
		echo FAILED."\n";
	}
}else{
	echo FAILED."\n";
}

// Descriptions cascadées
$display = "Description cascadée : 2 niveaux";
echo $display.niceDot($display);
$sql = "SELECT ED.description FROM events_description ED, events_isou EI WHERE ED.idEventDescription=EI.idEventDescription AND EI.idService=7";
$query = $db->prepare($sql);
$query->execute();
if($results = $query->fetch(PDO::FETCH_OBJ)){
	if($results->description === "Service web n°1 stoppé\nService web socle stoppé"){
		echo OK."\n";
	}else{
		echo FAILED."\n";
	}
}else{
	echo FAILED."\n";
}

// Descriptions cascadées sur un service parent non visible (ex: socle uportal)

?>
