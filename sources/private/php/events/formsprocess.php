<?php

/* * * * * * * * * * * * * * * * * *
	Converti une chaine "31/12/2010 14:30" en timestamp Unix ; sinon retourne NULL
* * * * * * * * * * * * * * * * * */
function strtotimestamp($date){
	if(preg_match('#^\d{1}/#', $date)){
		$date = '0'.$date;
	}

	if(preg_match('#^\d{2}/\d{1}/#', $date)){
		$date = substr($date,0,3).'0'.substr($date,3);
	}

	if(preg_match('#^\d{2}/\d{2}/\d{4} \d{1}:#', $date)){
		$date = substr($date,0,11).'0'.substr($date,11);
	}

	if(preg_match('#^\d{2}/\d{2}/\d{4} \d{2}:\d{1}$#', $date)){
		$date = substr($date,0,14).'0'.substr($date,14);
	}

	if(preg_match('#^\d{2}/\d{2}/\d{4} \d{2}:\d{2}$#',$date)){
		return mktime(intval(substr($date,11,2)), intval(substr($date,14,2)), 0, intval(substr($date,3,2)), intval(substr($date,0,2)), intval(substr($date,6,4)));
	}else{
		return NULL;
	}
}

/* * * * * * * * * * * * * * * * * *
	Annulation
* * * * * * * * * * * * * * * * * */
if(isset($_POST['cancel'])){
	header('Location: '.URL.'/index.php/evenements/'.$PAGE_NAME[1]);
	exit();
}


/* * * * * * * * * * * * * * * * * *
	Init vars
* * * * * * * * * * * * * * * * * */

require PRIVATE_PATH.'/classes/htmlpurifier/library/HTMLPurifier.auto.php';
$HTMLPurifier = new HTMLPurifier();

$error = NULL;

if(isset($_POST['name'])){
	$idService = intval($_POST['name']);
}else{
	$idService = 0;
}

if(isset($_POST['scheduled'])){
	if($_POST['scheduled'] === '2'){
		// regular
		$isScheduled = 2;
	}elseif($_POST['scheduled'] === '3'){
		// closed
		$isScheduled = 3;
		if($_POST['endDate'] === FALSE){
			$endDate = NULL;
		}
	}else{
		// scheduled
		$isScheduled = intval($_POST['scheduled']);
	}
}else{
	$isScheduled = 1;
}


if(isset($_POST['beginDate'])){
	$beginDate = strtotimestamp($_POST['beginDate']);
	if($beginDate !== NULL){
		$beginDate = strftime('%Y-%m-%dT%H:%M', strtotimestamp($_POST['beginDate']));
	}
}else{
	$beginDate = NULL;
}

if($beginDate === NULL){
	$beginDate = strftime('%Y-%m-%dT%H:%M', TIME);
	$_POST['beginDate'] = strftime('%d/%m/%Y %H:%M', TIME);
}

if(isset($_POST['endDate'])){
	$endDate = strtotimestamp($_POST['endDate']);
	if($endDate !== NULL){
		$endDate = strftime('%Y-%m-%dT%H:%M', $endDate);
	}
}else{
	$endDate = NULL;
}

if(isset($_POST['period']) && $isScheduled == 2){
	if($_POST['period'] == 'monthly'){
		$period = 7*24*60*60;
	}else if($_POST['period'] == 'weekly'){
		$period = 7*24*60*60;
	}else{
		// daily
		$period = 24*60*60;
	}
}else{
	$period = '';
}

if(isset($_POST['description'])){
	$description = $HTMLPurifier->purify($_POST['description']);
}else{
	$description = '';
}

if(isset($_POST['descriptionUpd'])){
	$descriptionUpd = $HTMLPurifier->purify($_POST['descriptionUpd']);
}else{
	$descriptionUpd = '';
}

if(isset($_POST['idEventDescription'])){
	$idEventDescription = intval($_POST['idEventDescription']);
	if($idEventDescription === 0 || $idEventDescription === 1){
		$idEventDescription = 1;
		$description = '';
	}
}

if(isset($_POST['idEvent'])){
	$idEvent = intval($_POST['idEvent']);
}else{
	$idEvent = 0;
}

if(isset($_GET['delete'])){
	$idDelete = intval($_GET['delete']);
}else{
	$idDelete = 0;
}

if(isset($_POST['idDelEvent'])){
	$idDelEvent = intval($_POST['idDelEvent']);
}else{
	$idDelEvent = 0;
}


/* * * * * * * * * * * * * * * * * *
  	Traitement d'un ajout
* * * * * * * * * * * * * * * * * */
if(isset($_POST['insert'])){

	// traitement de l'ajout d'un message informatif
	if(isset($_POST['message'])){
		$message = $HTMLPurifier->purify($_POST['message']);
		if(!empty($message)){
			$sql = "INSERT INTO events (idEvent, beginDate, endDate, typeEvent)".
					" VALUES(NULL, ?, ?, 2)";
			$query = $db->prepare($sql);
			if($query->execute(array($beginDate, $endDate))){
				$idEvent = $db->lastInsertId();
				$sql = "INSERT INTO events_info (idEventInfo, shortText, longText, idEvent)".
					" VALUES(NULL, ?, ?, ?)";
				$query = $db->prepare($sql);
				if($query->execute(array($message, $description, $idEvent))){
					$error = 'Le message a été inséré avec succès.';
					unset($_POST);
					add_log(LOG_FILE, NULL, 'INSERT', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$period.', '.$description.', '.$isScheduled.', '.$idService.')');
				}else{
					$error = 'Le message n\'a pas pu être inséré.';
				}
			}else{
				$error = 'Le message n\'a pas pu être inséré.';
			}
		}else{
			$error = 'Le champ "Message d\'information" est obligatoire';
		}
	}else{
	// traitement de l'ajout d'un evenement isou
		if($idService > 0 && ($endDate === NULL || $beginDate <= $endDate)){
			if($isScheduled == 0){
				$sql = "SELECT COUNT(E.idEvent) AS total FROM events E, events_isou EI".
						" WHERE E.idEvent=EI.idEvent".
						" AND EI.idService=?".
						" AND EI.isScheduled=0".
						" AND (E.endDate IS NULL OR".
						" (E.endDate >= ? AND E.beginDate <= ?))";
				$query = $db->prepare($sql);
				$query->execute(array($idService, TIME, TIME));
				$exist = $query->fetchObject();
				if($exist->total > 0){
					$error = 'Un évènement non prévu est déjà en cours pour ce service. Veuillez modifier ou supprimer l\'ancien évènement.';
				}
			}

			if($isScheduled == 3){
				$sql = "SELECT COUNT(E.idEvent) AS total FROM events E, events_isou EI".
						" WHERE E.idEvent=EI.idEvent".
						" AND EI.idService=?".
						" AND EI.isScheduled=3";
				$query = $db->prepare($sql);
				$query->execute(array($idService));
				$exist = $query->fetchObject();
				if($exist->total > 0){
					$error = 'Ce service possède déjà un évènement de fermeture. Veuillez modifier ou supprimer l\'ancien évènement.';
				}
			}

			if(!isset($error) && $isScheduled === 0){
				if(isset($_POST['forced'])){
					$forced = intval($_POST['forced']);
					if($forced >= 0 && $forced < 5){
						$sql = "UPDATE services SET state=?, readonly=1 WHERE idService = ?";
						$query = $db->prepare($sql);
						if($query->execute(array($forced, $idService)) === FALSE){
							$error = 'L\'évènement n\'a pas pu être inséré.';
						}else{
							if($forced === 0){
								$dontaddevent = TRUE;
							}
						}
					}
				}
			}

			$idEventDescription = 1;
			if(!isset($error) && !isset($dontaddevent) && !empty($description)){
				$sql = "SELECT idEventDescription FROM events_description WHERE autogen=0 AND description=?";
				$query = $db->prepare($sql);
				$query->execute(array(trim($description)));
				if($idEventDescription = $query->fetchObject()){
					$idEventDescription = $idEventDescription->idEventDescription;
				}else{
					$sql = "INSERT INTO events_description (idEventDescription, description, autogen)".
						" VALUES(NULL, ?, 0)";
					$query = $db->prepare($sql);
					if($query->execute(array(trim($description)))){
						$idEventDescription = $db->lastInsertId();
					}
				}
			}


			if($isScheduled === 2 && $endDate === NULL && !isset($dontaddevent)){
				$error = 'Veuillez indiquer une date de fin.';
			}

			if(!isset($error) && !isset($dontaddevent)){
				$sql = "INSERT INTO events (idEvent, beginDate, endDate, typeEvent)".
						" VALUES(NULL, ?, ?, 0)";
				$query = $db->prepare($sql);
				if($query->execute(array($beginDate, $endDate))){
					$idEvent = $db->lastInsertId();
					$sql = "INSERT INTO events_isou (idEventIsou, period, isScheduled, idService, idEvent, idEventDescription)".
						" VALUES(NULL, ?, ?, ?, ?, ?)";
					$query = $db->prepare($sql);
					if($query->execute(array($period, $isScheduled, $idService, $idEvent, $idEventDescription))){
						if($isScheduled == 3){
							$sql = "UPDATE services SET state=4 WHERE idService=?";
							$query = $db->prepare($sql);
							$query->execute(array($idService));
						}
						$sql = "SELECT DISTINCT idService FROM dependencies WHERE idServiceParent = ?";
						$query = $db->prepare($sql);
						$query->execute(array($idService));
						while($child = $query->fetch(PDO::FETCH_OBJ)){
							$sql = "INSERT INTO events_isou (idEventIsou, period, isScheduled, idService, idEvent, idEventDescription)".
								" VALUES(NULL, ?, ?, ?, ?, ?)";
							$insert = $db->prepare($sql);
							if(!$insert->execute(array($period, $isScheduled, $child->idService, $idEvent, $idEventDescription))){
								$error = 'L\'évènement n\'a pas pu être inséré.';
							}
						}

						if(!isset($error)){
							$error = 'L\'évènement a été inséré avec succès.';
							unset($_POST);
							add_log(LOG_FILE, NULL, 'INSERT', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$period.', '.$description.', '.$isScheduled.', '.$idService.')');
						}
					}else{
						$error = 'L\'évènement n\'a pas pu être inséré.';
					}
				}else{
					$error = 'L\'évènement n\'a pas pu être inséré.';
				}
			}
		}else{
			if($beginDate >= $endDate){
				$error = 'La date de début doit être inférieure à la date de fin';
			}else{
				$error = 'Remplir tous les champs (sauf "Raison de la maintenance" qui est facultatif)';
			}
		}
	}

	echo '<p id="update">'.$error.'</p>';
}

/* * * * * * * * * * * * * * * * * *
  	Traitement d'une suppression
* * * * * * * * * * * * * * * * * */
if(isset($_POST['delete']) && isset($idDelEvent)){
	if($idDelEvent > 0){
		$db->beginTransaction();
		$commit = FALSE;
		if(isset($_POST['message'])){
			$sql = "DELETE FROM events_info WHERE idEvent = ?";
		}else{
			$sql = "SELECT S.idService, S.state FROM services S, events_isou EI WHERE S.idService=EI.idService AND EI.idEvent = ?";
			$query = $db->prepare($sql);
			$query->execute(array($idDelEvent));
			$service = $query->fetchObject();
			$sql = "DELETE FROM events_isou WHERE idEvent = ?";
		}
		$query = $db->prepare($sql);
		if($query->execute(array($idDelEvent))){
			$sql = "DELETE FROM events ".
					" WHERE idEvent = ?";
			$query = $db->prepare($sql);
			if($query->execute(array($idDelEvent))){
				if(isset($_POST['message'])){
					$commit = TRUE;
				}else{
					if($service->state == '4'){
						$sql = "UPDATE services SET readonly=0, state=0 WHERE idService=?";
					}else{
						$sql = "UPDATE services SET readonly=0 WHERE idService=?";
					}
					$query = $db->prepare($sql);
					if($query->execute(array($service->idService))){
						$commit = TRUE;
					}
				}
			}
		}

		if($commit === TRUE){
			$db->commit();
			$error = 'L\'évènement #'.$_POST['idDelEvent'].' a été supprimée avec succès.';
		}else{
			$db->rollBack();
			$error = 'L\'évènement #'.$_POST['idDelEvent'].' n\'a pas pu être supprimée.';
			add_log(LOG_FILE, NULL, 'DELETE', 'Evènement #'.$_POST['idDelEvent']);
		}
	}else{
		$error = 'L\'évènement #'.$_POST['idDelEvent'].'n\'a pas pu être supprimée.';
	}
	echo '<p id="update">'.$error.'</p>';
}

/* * * * * * * * * * * * * * * * * *
  	Traitement d'une modification
* * * * * * * * * * * * * * * * * */
if(isset($_POST['modify'])){

	// traitement de la mise à jour d'un message informatif
	if(isset($_POST['message']) && !empty($_POST['message'])){
		$message = $HTMLPurifier->purify($_POST['message']);
		if(!empty($message)){
			$sql = "UPDATE events SET beginDate=?, endDate=? WHERE idEvent=?";
			$query = $db->prepare($sql);
			$updateMessage = $query->execute(array($beginDate, $endDate, $idEvent));
			$sql = "UPDATE events_info SET shortText=?, longText=? WHERE idEvent = ?";
			$query = $db->prepare($sql);
			$updateMessage = $updateMessage AND $query->execute(array($message, $description, $idEvent));

			if($updateMessage === TRUE){
				$error = 'Le message a été mis à jour avec succès.';
				unset($_POST);
				add_log(LOG_FILE, NULL, 'UPDATE', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$message.', '.$description.', '.$idEvent.')');
			}else{
				$error = 'Le message a été partiellement modifié.';
			}
		}else{
			$error = 'Le champ "Message d\'information" est obligatoire';
		}
	}else{
		// traitement de la mise à jour d'un evenement isou
		if($idService > 0 && !($endDate !== NULL && $beginDate >= $endDate)){
			$errorDescription = FALSE;
			if(empty($descriptionUpd)){
				$idEventDescription = 1;
			}else{
				$sql = "SELECT ED.idEventDescription, ED.description".
						" FROM events_isou EI, events_description ED".
						" WHERE ED.idEventDescription = EI.idEventDescription".
						" AND EI.idEvent=?";
				$query = $db->prepare($sql);
				if($query->execute(array($idEvent))){
					$eventDescription = $query->fetch();
					// vérifie si la description a changé
					if(isset($eventDescription[0])){
						if($eventDescription[0] !== $descriptionUpd){
							$sql = "SELECT ED.idEventDescription".
									" FROM events_description ED".
									" WHERE ED.description = :0";
							$query = $db->prepare($sql);
							// recherche éventuellement une description existante
							if($query->execute(array($descriptionUpd))){
								$newEventDescription = $query->fetch();
								if(isset($newEventDescription[0])){
									$idEventDescription = $newEventDescription[0];
								}else{
									// crée une description si elle n'existe pas
									$sql = "INSERT INTO events_description VALUES(NULL, :0, :1)";
									$query = $db->prepare($sql);
									if($query->execute(array($descriptionUpd, 0))){
										$idEventDescription = $db->lastInsertId();
									}else{
										$errorDescription = TRUE;
									}
								}
							}else{
								$errorDescription = TRUE;
							}
						}else{
							$idEventDescription = $eventDescription[0];
						}
					}else{
						$errorDescription = TRUE;
					}
				}else{
					$errorDescription = TRUE;
				}
			}

			if($errorDescription === FALSE){
				$sql = "UPDATE events_description SET description=? WHERE idEventDescription=?";
				$query = $db->prepare($sql);
				$updateDescription = $query->execute(array($descriptionUpd, $idEventDescription));
			}

			$sql = "UPDATE events SET beginDate=?, endDate=? WHERE idEvent=?";
			$query = $db->prepare($sql);
			$updateMessage = $query->execute(array($beginDate, $endDate, $idEvent));

			if($isScheduled === 0){
				if(isset($_POST['forced'])){
					if($endDate !== NULL && $endDate <= TIME){
						// supprimer le lock, si la date de fin est inférieure à la date actuelle
						$forced = -1;
					}else{
						$forced = intval($_POST['forced']);
					}
					if($forced >= 0 && $forced < 5){
						$sql = "UPDATE services SET state=?, readonly=1 WHERE idService = ?";
						$query = $db->prepare($sql);
						$updateMessage = $query->execute(array($forced, $idService));
					}else{
						$sql = "UPDATE services SET readonly=0 WHERE idService = ?";
						$query = $db->prepare($sql);
						$updateMessage = $query->execute(array($idService));
					}

					if($forced === 0){
						// supprimer tous les évènements en cours, si on force le service à l'état 'en fonctionnement'
						$sql = "DELETE FROM events_isou WHERE idEvent = ?";
						$query = $db->prepare($sql);
						$query->execute(array($idEvent));

						$sql = "DELETE FROM events WHERE idEvent = ?";
						$query = $db->prepare($sql);
						$query->execute(array($idEvent));
					}
				}
			}elseif($isScheduled == 3){
				$sql = "UPDATE services SET state=4 WHERE idService=?";
				$query = $db->prepare($sql);
				$query->execute(array($idService));
			}

			$sql = "UPDATE events_isou SET period=?, idEventDescription=?, isScheduled=?, idService=? WHERE idEvent=?";
			$query = $db->prepare($sql);
			$updateMessage = $updateMessage AND $query->execute(array($period, $idEventDescription, $isScheduled, $idService, $idEvent));

			if($updateMessage === TRUE){
				$error = 'L\'évènement a été mise à jour avec succès.';
				unset($_POST);
				add_log(LOG_FILE, NULL, 'UPDATE', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$period.', '.$description.', '.$isScheduled.', '.$idService.')');
			}else{
				$error = 'L\'évènement n\'a pas pu être mis à jour.';
			}
		}else{
			if($beginDate >= $endDate){
				$error = 'La date de début doit être inférieur à la date de fin';
			}else{
				$error = 'Remplir tous les champs (sauf "Raison de la maintenance" qui est facultatif)';
			}
		}
	}

	echo '<p id="update">'.$error.'</p>';
}

?>
