<?php

/* * * * * * * * * * * *
 * RECUPERATION DES ETATS DANS NAGIOS
 * * * * * * * * * * * */

// MISE EN CACHE : parsing du status.dat ou utilisation de la bdd
function update_nagios_to_db(){

	try {
		$db = new PDO(DB_PATH, '', '');
	} catch (PDOException $e) {
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', $e->getMessage());
	}

	// parse le status.dat et mets à jour les états dans la base de données
	$log = status_dat2db(STATUSDAT_URL);

	if($log instanceof Exception){
		// close pdo connection
		$db = null;
		return $log;
		exit;
	}

	// reactive tous les services finaux
	$sql = "UPDATE services".
		" SET state = 0".
		" WHERE name = 'Service final'".
		" AND readonly = 0";
	$query = $db->prepare($sql);
	if($query->execute()){
		if($query->rowCount() > 0){
			// add_log(LOG_FILE, 'ISOU', 'update', $query->rowCount().' services finaux ont été réinitialisés à l\'état 0');
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'Les services finaux n\'ont pas pu être réinitialisés à l\'état 0');
	}

	/* * * * * * * * * * * * * * * * * * *
	 * Fonction récursive pour gérer les dépendances
	 * * * * * * * * * * * * * * * * * * */
	// selectionne tous les services Nagios dont l'état est 1, 2 ou 3
	$sql_count = "SELECT COUNT(*)".
				" FROM services".
				" WHERE state BETWEEN 1 AND 3";

	if($queryCount = $db->query($sql_count)){
		$count = $queryCount->fetchAll();
		$queryCount->closeCursor();
		$cnt = 0;
		$boucle = 0;
		while($cnt != $count[0][0]){
			$boucle++;
			$cnt = $count[0][0];
			// recherche des dépendances, et modifie les états fils en conséquence
			$sql = "SELECT idService, state".
					" FROM services".
					" WHERE state BETWEEN 1 AND 3";
			$array = array();
			if($dependence_records = $db->query($sql)){
				$dependence = $dependence_records->fetchAll();

				$d = 0;
				while(isset($dependence[$d][0])){
					$array = make_dependencies($dependence[$d][0],$dependence[$d][1], $array, $db);
					$array = array_unique($array);
					$d++;
				}
			}else{
				add_log(LOG_FILE, 'ISOU', 'update', 'Les dépendances du service '.$dependence[$d][0].' n\'ont pas pu être mises à jour');
			}

			if($queryCount = $db->query($sql_count)){
				$count = $queryCount->fetchAll();
				$queryCount->closeCursor();
			}else{
				$count[0][0] = $cnt;
			}

		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'Les dépendances n\'ont pas pu être mises à jour');
	}

	if(isset($array)){
		$array_dependence = $array;
	}else{
		$array_dependence = array();
	}

	// ajoute les interruptions de services non prévues dans la base de données
	$d = 0;
	// tous les services dont l'état est superieur a zero et qui n'ont pas evenements en cours
	$sql = "SELECT idService".
		" FROM services".
		" WHERE state BETWEEN 1 AND 3".
		" AND nameForUsers IS NOT NULL".
		" AND enable = 1".
		" AND idService NOT IN(SELECT EI.idService FROM events E, events_isou EI WHERE E.endDate IS NULL AND E.idEvent = EI.idEvent)";

	if($dependence_records = $db->query($sql)){
		$dependence = $dependence_records->fetchAll();
		$dependence_records->closeCursor();
		while(isset($dependence[$d][0])){
			$addEvent=false;
			$sql = "SELECT E.beginDate, E.endDate".
					" FROM events E, events_isou EI".
					" WHERE E.idEvent = EI.idEvent".
					" AND EI.idService = ".$dependence[$d][0];
			if($event_records = $db->query($sql)){
				$event_record = $event_records->fetch();
				$i = 0;
				$coverEvent = false;
				while($event_record && !$coverEvent){
					if(is_null($event_record[1])){
						$event_record[1] = 0;
					}
					if(($event_record[0] <= TIME && $event_record[1] >= TIME)
							 || ($event_record[0] <= TIME && $event_record[1] == 0)
							){
						$coverEvent = true;
					}
					$event_record = $event_records->fetch();
					$i++;
				}

				if(!$coverEvent){
					// ajout d'un evenement non prevu
					$description = NULL;
					if(isset($array_dependence[$dependence[$d][0]])){
						if(count($array_dependence[$dependence[$d][0]]) > 0){
							$description = "";
							foreach($array_dependence[$dependence[$d][0]] as $descript){
								$description .= $descript."\n";
							}
							$description = substr($description, 0, -1);
						}
					}

					$db->beginTransaction();
					$commit = FALSE;
					$sql = "INSERT INTO events(beginDate, endDate, typeEvent)".
						" VALUES(?, NULL, 0)";
					$query = $db->prepare($sql);
					if($query->execute(array(TIME))){
						$idEvent = $db->lastInsertId();
						if($description === NULL){
							$description = 1;
						}else{
							$sql = "INSERT INTO events_description(description, autogen)".
									" VALUES(:0, 1)";
							$query = $db->prepare($sql);
							if($query->execute(array($description))){
								$description = $db->lastInsertId();
							}else{
								$description = 1;
							}
						}
						$sql = "INSERT INTO events_isou(period, isScheduled, idService, idEvent, idEventDescription)".
							" VALUES(NULL, 0, ?, ?, ?)";
						$query = $db->prepare($sql);
						if($query->execute(array($dependence[$d][0], $idEvent, $description))){
							$commit = TRUE;
						}
					}

					if($commit === TRUE){
						$db->commit();
						if($query->rowCount() > 0){
							add_log(LOG_FILE, 'ISOU', 'update', 'Nombre de lignes modifiées (nouvel évènement non prévu) : '. $query->rowCount());
						}
					}else{
						$db->rollBack();
						add_log(LOG_FILE, 'ISOU', 'update', 'L\'interruption du service '.$dependence[$d][0].' n\'a pas pu être ajouté');
					}

					$query->closeCursor();
				}
				$d++;
			}else{
				add_log(LOG_FILE, 'ISOU', 'update', 'Les évènements du service '.$dependence[$d][0].' n\'ont pas pu être traités');
			}
			$event_records->closeCursor();
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'L\'ajout des nouvelles interruptions n\'a pas pu être effectué');
	}

	// met à jour la description des évènements ayant un évènement en cours
	/*$sql = "SELECT EI.idService".
		" FROM events E, events_isou EI".
		" WHERE endDate IS NULLBETWEEN 1 AND 3".
		" AND nameForUsers IS NOT NULL".
		" AND enable = 1".
		" AND visible = 1";
	if($event_records = $db->query($sql)){
		$event_record = $event_records->fetch();
*/

	// supprime le "lock" si une interruption non prévue est terminée
	$sql = "UPDATE services".
			" SET state = 0, readonly = 0".
			" WHERE readonly=1".
			" AND idService NOT IN (SELECT s.idService".
									" FROM events e, events_isou ei, services s".
									" WHERE readonly = 1".
									" AND ei.idService = s.idService".
									" AND e.idEvent = ei.idEvent".
									" AND ((e.beginDate < ".TIME." AND e.endDate > ".TIME.") OR e.endDate is null)".
									")";
	$query = $db->prepare($sql);
	if($query->execute(array())){
		if($query->rowCount() > 0){
			add_log(LOG_FILE, 'ISOU', 'update', 'Nombre de lignes modifiées (réactivation de services) : '.$query->rowCount());
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'La réactivation des services revenus à l\'état de marche n\'a pas pu être effectuée');
	}
	$query->closeCursor();


	// ajoute une date de réactivation d'un service interrompu involontairement si leur état est à 0
	// ou si il y a un évènement prévu qui commence, alors qu'il y a un le meme service qui possede un evenement imprevu
	$sql = "UPDATE events".
			" SET endDate = :0".
			" WHERE idEvent IN (SELECT E.idEvent".
								" FROM events E, events_isou EI, services S".
								" WHERE S.idService = EI.idService".
								" AND E.idEvent = EI.idEvent".
								" AND S.readonly = 0".
								" AND E.typeEvent = 0".
								" AND E.endDate IS NULL".
								" AND (".
										"(".
											// évènement non prévu dont le service est à l'état 0
											"E.beginDate < ".TIME.
											" AND S.state = 0".
											" AND E.endDate IS NULL".
											" AND EI.isScheduled = 0".
										") OR (".
											// services qui ont un évènement prévu en cours
											"EI.idService = (SELECT EI.idService".
															" FROM events E, events_isou EI".
															" WHERE E.idEvent = EI.idEvent".
															" AND EI.isScheduled = 1".
															" AND E.beginDate <= ".TIME.
															" AND (E.endDate > ".TIME.
															" OR E.endDate IS NULL)".
															")".
										")".
									")".
							")".
			" AND endDate IS NULL";
	$query = $db->prepare($sql);
	if($query->execute(array(TIME))){
		if($query->rowCount() > 0){
			add_log(LOG_FILE, 'ISOU', 'update', 'Nombre de lignes modifiées (réactivation de services) : '.$query->rowCount());
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'La réactivation des services revenus à l\'état de marche n\'a pas pu être effectuée');
	}
	$query->closeCursor();


	// change le temps de la prochaine interruption régulière
	$sql = "SELECT idEvent, period FROM events_isou WHERE isScheduled = 2";
	$events = $db->prepare($sql);
	$cntUpd = 0;
	if($events->execute()){
		while($event = $events->fetch(PDO::FETCH_OBJ)){
			$sql = "UPDATE events".
					" SET beginDate=(beginDate+:0), endDate=(endDate+:0)".
					" WHERE endDate < :1".
					" AND idEvent = :2".
					" AND typeEvent = 0";
			$query = $db->prepare($sql);

			if(!empty($event->period) && $query->execute(array($event->period, TIME, $event->idEvent))){
				if($query->rowCount() > 0){
					while($query->rowCount() > 0){
						$query->closeCursor();
						$query = $db->prepare($sql);
						$query->execute(array($event->period, TIME, $event->idEvent));
					}
					$cntUpd+;
				}
			}else{
				add_log(LOG_FILE, 'ISOU', 'update', 'La mise à jour des dates des interruptions régulières n\'a pas pu être effectuée pour l\évènement #'.$event->idEvent);
			}
		}
		$query->closeCursor();
		if($cntUpd > 0){
			add_log(LOG_FILE, 'ISOU', 'update', 'Nombre de lignes modifiées (interruption régulière) : '.$cntUpd);
		}
	}

	// change le statut des services dont une plannification de fermeture a été prévue
	$sql = "UPDATE services".
			" SET state = 4, readonly = 1".
			" WHERE readonly = 0".
			" AND idService IN (SELECT EI.idService".
									" FROM events E, events_isou EI".
									" WHERE E.idEvent = EI.idEvent".
									" AND E.typeEvent = 0".
									" AND E.beginDate < :0".
									" AND (E.endDate > :0".
									" OR E.endDate IS NULL)".
									" AND EI.isScheduled = 3)";
	$query = $db->prepare($sql);
	if($query->execute(array(TIME))){
		if($query->rowCount() > 0){
			add_log(LOG_FILE, 'ISOU', 'update', 'Nombre de lignes modifiées (fermeture de service) : '. $query->rowCount());
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'La mise à jour des fermetures de service n\'a pas pu être effectuée');
	}

	// réactive les services dont une plannification de fermeture a été prévue et est finie
	$sql = "UPDATE services".
			" SET state = 0, readonly = 0".
			" WHERE readonly = 1".
			" AND state = 4".
			" AND idService NOT IN (SELECT EI.idService".
									" FROM events E, events_isou EI".
									" WHERE E.idEvent = EI.idEvent".
									" AND E.typeEvent = 0".
									" AND E.beginDate < :0".
									" AND (E.endDate > :0".
									" OR E.endDate IS NULL)".
									" AND EI.isScheduled = 3)";
	$query = $db->prepare($sql);
	if($query->execute(array(TIME))){
		if($query->rowCount() > 0){
			add_log(LOG_FILE, 'ISOU', 'update', 'Nombre de lignes modifiées (réouverture de service) : '. $query->rowCount());
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'update', 'La mise à jour des réouvertures de service n\'a pas pu être effectuée');
	}

	// close pdo connection
	$db = null;

	return true;
}

// fonction recursive qui retourne les messages automatiques liés aux dépendances
// met à jour l'état du service à l'instant T
function make_dependencies($idParent,$stateParent,$array,$db){

	$sql = "SELECT idService, newStateForChild, message".
	" FROM dependencies".
	" WHERE idServiceParent = ".$idParent.
	" AND stateOfParent = ".$stateParent;

	if($depend_records = $db->query($sql)){
		//SQLITE_NUM
		$depend = $depend_records->fetchAll();
		$d = 0;
		while(isset($depend[$d][0])){
			$sql = "UPDATE services".
					" SET state = ".$depend[$d][1].
					" WHERE idService = ".$depend[$d][0].
					" AND state < ".$depend[$d][1].
					" AND readonly = 0";
			if($db->query($sql)){
				if(!isset($array[$depend[$d][0]])){
					$array[$depend[$d][0]] = array();
				}

				if(!empty($depend[$d][2]) && !in_array($depend[$d][2], $array[$depend[$d][0]])){
					$array[$depend[$d][0]][] = $depend[$d][2];
				}

				$array = make_dependencies($depend[$d][0], $depend[$d][1], $array, $db);
			}else{
				add_log(LOG_FILE, 'ISOU', 'error', 'L\'état du service #'.$depend[$d][0].' n\'a pas pu être mis à jour (boucle dépendance)');
			}
			$d++;
		}
	}else{
		add_log(LOG_FILE, 'ISOU', 'error', 'Les dépendances du service #'.$idParent.' n\'ont pas pu être calculées');
	}

	return $array;
}

?>
