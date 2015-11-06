<?php

// notes à afficher en dernière, après mise à jour
$notes = array();

// typage du numéro de version
// #bug: problème avec LC_ALL, fr_FR.utf-8
// $version = (float) substr(VERSION, 0, strpos(VERSION, '.')+1).str_replace('.', '', substr(VERSION, strpos(VERSION, '.')+1));
$version =(float) substr(VERSION, 0, strpos(VERSION, '.')).'.'.str_replace('.', '', substr(VERSION, strpos(VERSION, '.')+1));

// mise à jour pour les versions antérieures à la 0.9.5
if($version <= 0.95){
	echo "\033[0;35m\nNouvelles variables :\033[0m\n";
	echo "\nQuelles sont les adresses mails devant recevoir des alertes mails (séparés par des virgules) ?\n";
	echo "Défaut: \033[1;34mvide\033[0m\n";
	echo "exemple : \033[1;30mexample1@example.com, example2@example.com\033[0m\n";
	$ADMIN_MAILS = trim(fgets(STDIN));
	if(empty($ADMIN_MAILS)){
		$ADMIN_MAILS = 'array()';
	}else{
		$mails = explode(',', $ADMIN_MAILS);
		$ADMIN_MAILS = '';
		foreach($mails as $mail){
			$ADMIN_MAILS .= '\''.trim($mail).'\',';
		}
		$ADMIN_MAILS = 'array('.substr($ADMIN_MAILS, 0, -1).');';
	}
	$ADMIN_MAILS = "// tableau contenant le mail des administrateurs d'ISOU\n".$ADMIN_MAILS;
	$LOG_LEVEL = "// niveau verbeux des logs\n// valeurs possibles : 0=muet, 1=production, 2=debug\ndefine('LOG_LEVEL', 1);";

	// mise à jour du fichier ./config.php
	$update_cfg = FALSE;
	$cfg = file_get_contents($config);
	if(!empty($cfg)){
		$cfg = str_replace("?>", $ADMIN_MAILS."\n\n".$LOG_LEVEL."\n\n?>", $cfg);
		$update_cfg = file_put_contents($config, $cfg);
	}

	$display = "\nMise à jour du fichier ".PRIVATE_PATH."/config.php";
	if($update_cfg !== FALSE){
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
		echo "\033[0;31mMerci d'ajouter manuellement les changements dans le fichier de configuration.\033[0m\n";
	}
}

if($version <= 0.96){
	// création de la table configuration
	$sql = "CREATE TABLE configuration (key VARCHAR(64) PRIMARY KEY, value VARCHAR(256))";
	if($db->exec($sql) === FALSE){
		echo "La création de la table 'configuration' a échoué.\n";
		echo "\033[0;31mÉchec de l'installation\033[0m\n";
		exit(1);
	}else{
		$display = "Création de la table 'configuration'";
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	// insertion des écritures dans la table configuration
	$sql = "INSERT INTO configuration(key, value) VALUES('tolerance','".TOLERANCE."')";
	$display = '   Insertion de la clé "tolérance" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('ip_local','".json_encode($IP_INTERNE)."')";
	$display = '   Insertion de la clé "ip_local" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('ip_service','".json_encode($IP_CRI)."')";
	$display = '   Insertion de la clé "ip_service" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('admin_users', '".json_encode($ADMIN_USERS)."')";
	$display = '   Insertion de la clé "admin_users" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('admin_mails', '".json_encode($ADMIN_MAILS)."')";
	$display = '   Insertion de la clé "admin_mails" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('version', '".VERSION."')";
	$display = '   Insertion de la clé "version" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('last_update', '".TIME."')";
	$display = '   Insertion de la clé "last_update" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('last_check_update', '".TIME."')";
	$display = '   Insertion de la clé "last_check_update" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('last_cron_update', '0')";
	$display = '   Insertion de la clé "last_cron_update" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('last_daily_cron_update', '0')";
	$display = '   Insertion de la clé "last_daily_cron_update" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('daily_cron_hour', '06:00')";
	$display = '   Insertion de la clé "daily_cron_hour" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('last_weekly_cron_update', '".TIME."')";
	$display = '   Insertion de la clé "last_weekly_cron_update" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('last_yearly_cron_update', '".TIME."')";
	$display = '   Insertion de la clé "last_yearly_cron_update" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "INSERT INTO configuration(key, value) VALUES('local_password', '')";
	$display = '   Insertion de la clé "local_password" dans la table configuration'; 
	if($db->exec($sql) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	$sql = "SELECT EI.idService, COUNT(E.idEvent) AS total FROM events E, events_isou EI".
			" WHERE E.idEvent=EI.idEvent".
			" AND EI.isScheduled=3".
			" GROUP BY EI.idService";
	$query = $db->prepare($sql);
	$query->execute();
	while($service = $query->fetchObject()){
		if($service->total > 1){
			$sql = "SELECT E.idEvent, EI.idEventIsou FROM events E, events_isou EI".
					" WHERE E.idEvent=EI.idEvent".
					" AND EI.isScheduled=3".
					" ORDER BY E.beginDate DESC";
			$events = $db->prepare($sql);
			$events->execute();
			// skip first
			$event = $events->fetchObject();
			while($event = $events->fetchObject()){
				$sql = "DELETE FROM events WHERE idEvent=?";
				$delete = $db->prepare($sql);
				$delete->execute(array($event->idEvent));
				$sql = "DELETE FROM events_isou WHERE idEventIsou=?";
				$delete = $db->prepare($sql);
				$delete->execute(array($event->idEventIsou));
			}
			$display = 'Suppression des évènements de fermeture de service (doublons uniquement)';
			echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
		}
	}
	$notes[] = 'Version 0.9.6';
	$notes[] = '-------------';
	$notes[] = 'Attention ! La procédure pour appeler les crons a été modifiée.';
	$notes[] = 'Merci de supprimer tous les appels aux crons d\'Isou précédemment installés et d\'ajouter un appel (toutes les 5 minutes ou moins) à ce fichier : '.$public_path.'/cron.php';
	$notes[] = '';
}

// $sql = "UPDATE configuration SET value=? WHERE key='version'";
// $sql = "UPDATE configuration SET value=? WHERE key='last_update'";

if(count($notes) > 0){
	echo "\nNote de mise à jour !";
	foreach($notes as $note){
		echo $note."\n";
	}
}

?>
