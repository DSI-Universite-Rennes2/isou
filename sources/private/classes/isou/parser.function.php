<?php

/* * * * * * * * * * * * * * * * * *
 * Parse le fichier status.dat de Nagios, et mets à jour les status dans Isou
 * Retourne une chaine de caracteres, contenant les modifications effectuees, lorsque la fonction est appelé par Cron
 * * * * * * * * * * * * * * * * * */

function status_dat2db($file){

	if($handle = @fopen($file, "r")){
		try {
			$DB = new PDO(DB_PATH, '', '');
		} catch (PDOException $e) {
			// close pdo connection
			$DB = null;
			return new Exception("Impossible d'ouvrir la base de données ".DBPATH);
			exit;
		}
	}else{
		return new Exception("Impossible d'ouvrir le fichier ".$file);
		exit;
	}

	$cron_message = '';
	$services_nagios = array();

	while(!feof($handle)){
		$tp =  trim(fgets($handle, 4096));
		if(preg_match('#hoststatus \{|servicestatus \{#',$tp)){
			$tag = substr($tp,0,-2);
			$continue=true;
			$host_name = "";
			$service_description = "";
			$check_command = "";
			$current_state = 0;
			$problem_has_been_acknowledged = 0;
			while(!feof($handle) && $continue){
				$tp =  trim(fgets($handle, 4096));
				if(!preg_match('#}#',$tp)){
					if(preg_match('#host_name=|service_description=|check_command=|current_state=|problem_has_been_acknowledged=|is_flapping=#',$tp)){
						$split = explode('=',$tp);
						switch($split[0]){
							case 'host_name': $host_name=$split[1];break;
							case 'service_description': $service_description=$split[1];break;
							case 'check_command': $check_command=$split[1];break;
							case 'current_state': $current_state=$split[1];break;
							case 'problem_has_been_acknowledged' : $problem_has_been_acknowledged=$split[1];break;
							case 'is_flapping' : $is_flapping=$split[1];break;
						}
					}
				}else{
					$continue=false;
				}
			}

			// passe le service en rouge si il est en "flapping"
			if($is_flapping == 1){
				$current_state = 2;
			}

			// passe le service en vert si le problème est connu
			if($problem_has_been_acknowledged == 1){
				$problem_has_been_acknowledged = 0;
				$current_state = 0;
			}

			if($problem_has_been_acknowledged == 0){
				if($tag == 'servicestatus'){
					$host_name = $service_description.'@'.$host_name;
				}

				$services_nagios[] = $host_name;

				if($current_state > 0){
					$cron_message .= "Etat $current_state : $host_name \n";
				}

				$sql = "UPDATE services".
						" SET state = ".$current_state.
						" WHERE name = '".$host_name."'".
						" AND readonly = 0";
				$query = $DB->prepare($sql);
				if($query->execute()){
					if($query->rowCount()>0){
						// add_log(LOG_FILE, 'ISOU', 'UPDATE', 'Nombre de lignes modifiées (services '.$host_name.' mis à jour dans la db) : '. $query->rowCount());
					}
				}else{
					add_log(LOG_FILE, 'ISOU', 'UPDATE', 'L\'écriture du service '.$host_name.' n\'a pas pu être mise à jour');
				}

				$sql = "SELECT idService".
						" FROM services S".
						" WHERE name = '".$host_name."'".
						" AND readonly = 0";
				$services = $DB->query($sql);

				while($service = $services->fetch()){
					$sql = "SELECT EN.idEventNagios, EN.state".
							" FROM events E, events_nagios EN".
							" WHERE E.idEvent = EN.idEvent".
							" AND E.typeEvent = 1".
							" AND EN.idService = ".$service[0].
							" AND E.endDate IS NULL";
					$events_nagios = $DB->query($sql);
					$ins = true;
					$event_nagios = $events_nagios->fetch();
					$events_nagios->closeCursor();
					if(isset($event_nagios[0])){
						if($event_nagios[1] != $current_state){
							// $sql = "UPDATE events_nagios SET endDate = '".TIME."' WHERE idEventNagios = ".$event_nagios[0];
							$sql = "UPDATE events SET endDate = '".strftime('%Y-%m-%dT%H:%M', TIME)."'".
									" WHERE typeEvent = 1 AND idEvent = (SELECT idEvent FROM events_nagios WHERE idEventNagios = ".$event_nagios[0].")";
							$upd_nagios = $DB->prepare($sql);

							if($upd_nagios->execute()){
								add_log(LOG_FILE, 'ISOU', 'UPDATE', 'L\'évènement Nagios #'.$event_nagios[0].' a été cloturé (SET endDate = '.TIME.' WHERE idEventNagios = '.$event_nagios[0].')');
							}else{
								add_log(LOG_FILE, 'ISOU', 'UPDATE', 'L\'évènement Nagios #'.$event_nagios[0].' n\'a pas été cloturé (SET endDate = '.TIME.' WHERE idEventNagios = '.$event_nagios[0].')');
								$ins = false;
							}
							$upd_nagios->closeCursor();
						}else{
							$ins = false;
						}
					}

					if($current_state != '0' && $ins){
						$ins = FALSE;
						/*
						try{
							$DB->beginTransaction();
						}catch(PDOException $e){
							var_dump($e);
							die();
						}
						*/
						$sql = "INSERT INTO events(beginDate, endDate, typeEvent)".
								" VALUES(?, NULL, 1)";
						$query = $DB->prepare($sql);
						if($query->execute(array(strftime('%Y-%m-%dT%H:%M', TIME)))){
							$idEvent = $DB->lastInsertId();
							$sql = "INSERT INTO events_nagios (state, idService, idEvent)".
									" VALUES(:0, :1, :2)";
							$query = $DB->prepare($sql);
							if($query->execute(array($current_state, $service[0], $idEvent))){
								$ins = TRUE;
							}
							$query->closeCursor();
						}

						// dirty hack pour le problème :
						// Fatal error: Uncaught exception 'PDOException' with message 'There is already an active transaction'
						// This should be specific to SQLite, sleep for 0.25 seconds
						// and try again.  We do have to commit the open transaction first though
						// usleep(250000);

						if($ins === TRUE){
							// $DB->commit();
							add_log(LOG_FILE, 'ISOU', 'INSERT', 'Un évènement Nagios a été inséré (VALUES(NULL, '.TIME.', NULL, '.$current_state.', '.$service[0].'))');
						}else{
							// $DB->rollBack();
							add_log(LOG_FILE, 'ISOU', 'INSERT', 'Un évènement Nagios n\'a pas été inséré (VALUES(NULL, '.TIME.', NULL, '.$current_state.', '.$service[0].'))');
						}
					}

				}
			}


		}
	}
	fclose($handle);

	$removed_services_nagios = '';
	foreach($services_nagios as $service_nagios){
		$sql = "SELECT name, nameForUsers FROM services WHERE name = ?";
		$query = $DB->prepare($sql);
		if($query->execute(array($service_nagios)) === FALSE){
			$removed_services_nagios .= $service_nagios."\n";
		}
	}

	// close pdo connection
	$DB = null;

	return true;
}

?>
