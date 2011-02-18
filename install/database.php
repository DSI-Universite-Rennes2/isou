<?php

/*
 * 1. TABLE states
 */
$sql = "CREATE TABLE states (".
		" idState TINYTEXT,".
		" name  VARCHAR ( 32 ),".
		" title VARCHAR ( 32 ),".
		" alt VARCHAR ( 32 ),".
		" src VARCHAR ( 32 ))";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'states' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'states'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";

	$error = FALSE;
	$sql = "INSERT INTO states(idState, name, title, alt, src)".
			" VALUES(0, 'ok', 'Service en fonctionnement', 'Service en fonctionnement', 'flag_green.gif')";
	$insert = $db2->prepare($sql);
	if($insert->execute(array()) === FALSE){
		$error = TRUE;
	}

	$sql = "INSERT INTO states(idState, name, title, alt, src)".
			" VALUES(1, 'warning', 'Service instable ou indisponible', 'Service instable ou indisponible', 'flag_orange.gif')";
	$insert = $db2->prepare($sql);
	if($insert->execute(array()) === FALSE){
		$error = TRUE;
	}

	$sql = "INSERT INTO states(idState, name, title, alt, src)".
			" VALUES(2, 'critical', 'Service indisponible', 'Service indisponible', 'flag_red.gif')";
	$insert = $db2->prepare($sql);
	if($insert->execute(array()) === FALSE){
		$error = TRUE;
	}

	$sql = "INSERT INTO states(idState, name, title, alt, src)".
			" VALUES(3, 'unknown', 'Etat du service non connu', 'Etat du service non connu', 'flag_blue.gif')";
	$insert = $db2->prepare($sql);
	if($insert->execute(array()) === FALSE){
		$error = TRUE;
	}

	$sql = "INSERT INTO states(idState, name, title, alt, src)".
			" VALUES(4, 'closed', 'Service fermé', 'Service fermé', 'flag_white.gif')";
	$insert = $db2->prepare($sql);
	if($insert->execute(array()) === FALSE){
		$error = TRUE;
	}

	if($error === FALSE){
		$display = "   Insertion des écritures dans 'states'";
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo "   L'insertion des écritures dans la table 'states' a échoué.\n";
		echo "\033[0;31mÉchec de l'installation\033[0m\n";
		exit(1);
	}
}

/*
 * 2. TABLE annonce
 */
$sql = "CREATE TABLE annonce (".
		" message TINYTEXT,".
		" afficher INT)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'annonce' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'annonce'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 3. TABLE categories
 */
$sql = "CREATE TABLE categories (".
		" idCategory INTEGER PRIMARY KEY,".
		" name VARCHAR ( 32 ),".
		" position TINYINT ( 4 ))";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'categories' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'categories'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 4. TABLE dependencies
 */
$sql = "CREATE TABLE dependencies (".
		" idDependence INTEGER PRIMARY KEY,".
		" message TEXT,".
		" newStateForChild TINYINT,".
		" stateOfParent TINYINT,".
		" idService TINYINT,".
		" idServiceParent TINYINT)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'dependencies' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'dependencies'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 5. TABLE events
 */
$sql = "CREATE TABLE events (".
		" idEvent INTEGER PRIMARY KEY,".
		" beginDate TIMESTAMP,".
		" endDate TIMESTAMP,".
		" typeEvent INTEGER)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'events' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'events'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 6. TABLE events_isou
 */
$sql = "CREATE TABLE events_isou (".
		" idEventIsou INTEGER PRIMARY KEY,".
		" period INT,".
		" isScheduled TINYINT NOT NULL DEFAULT '1',".
		" idService TINYINT,".
		" idEventDescription TINYINT,".
		" idEvent TINYINT)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'events_isou' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'events_isou'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 7. TABLE events_nagios
 */
$sql = "CREATE TABLE events_nagios (".
		" idEventNagios INTEGER PRIMARY KEY,".
		" state TINYINT,".
		" idService INTEGER,".
		" idEvent INTEGER)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'events_nagios' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'events_nagios'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 8. TABLE events_info
 */
$sql = "CREATE TABLE events_info (".
		" idEventInfo INTEGER PRIMARY KEY,".
		" shortText TEXT,".
		" longText TEXT,".
		" idEvent INTEGER)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'events_info' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'events_info'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 9. TABLE events_description
 */
$sql = "CREATE TABLE events_description(".
		" idEventDescription INTEGER PRIMARY KEY,".
		" description TEXT,".
		" autogen TINYINT)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'events_description' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'events_description'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";

	$sql = "INSERT INTO events_description(idEventDescription, description, autogen)".
			" VALUES(1, '', 1)";
	$insert = $db2->prepare($sql);
	if($insert->execute(array()) === TRUE){
		$display = "   Insertion des écritures dans 'events_description'";
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo "   L'insertion des écritures dans la table 'events_description' a échoué.\n";
		echo "\033[0;31mÉchec de l'installation\033[0m\n";
		exit(1);
	}

}

/*
 * 10. TABLE statistics
 */
$sql = "CREATE TABLE statistics (session_id VARCHAR(64),os VARCHAR(32),browser VARCHAR(32),ip VARCHAR(15),userAgent TEXT,dateVisit TIMESTAMP)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'statistics' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'statistics'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

/*
 * 11. TABLE services
 */
$sql = "CREATE TABLE services (idService INTEGER PRIMARY KEY,name VARCHAR(64),nameForUsers VARCHAR(64),url VARCHAR(64), state TINYINT,comment VARCHAR(64),enable TINYINT,visible TINYINT,readonly TINYINT,rssKey TINYINT ,idCategory TINYINT)";
if($db2->exec($sql) === FALSE){
	echo "La création de la table 'services' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la table 'services'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

$sql = "CREATE UNIQUE INDEX services_rssKey ON services(rssKey)";
if($db2->exec($sql) === FALSE){
	echo "La création de la contrainte de la table 'services' a échoué.\n";
	echo "\033[0;31mÉchec de l'installation\033[0m\n";
	exit(1);
}else{
	$display = "Création de la contrainte de la table 'services'";
	echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
}

?>
