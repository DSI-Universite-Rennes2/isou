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
	header('Location: '.URL.'/index.php/evenements');
	exit();
}

/* * * * * * * * * * * * * * * * * *
	Init vars
* * * * * * * * * * * * * * * * * */

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
		if($endDate === FALSE){
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
}

if($beginDate === NULL){
	$beginDate = TIME;
	$_POST['beginDate'] = strftime('%d/%m/%Y %H:%M', TIME);
}

if(isset($_POST['endDate'])){
	$endDate = strtotimestamp($_POST['endDate']);
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
	$description = trim($_POST['description']);
}else{
	$description = '';
}

if(isset($_POST['descriptionUpd'])){
	$descriptionUpd = trim($_POST['descriptionUpd']);
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
		$message = trim($_POST['message']);
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
					add_log(LOG_FILE, phpCAS::getUser(), 'INSERT', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$period.', '.$description.', '.$isScheduled.', '.$idService.')');
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
		if($idService > 0 && !($endDate !== NULL && $beginDate >= $endDate)){

			$idEventDescription = 1;
			if(!empty($description)){
				$sql = "INSERT INTO events_description (idEventDescription, description, autogen)".
					" VALUES(NULL, ?, 0)";
				$query = $db->prepare($sql);
				if($query->execute(array($description))){
					$idEventDescription = $db->lastInsertId();
				}
			}

			if($isScheduled === 0){
				if(isset($_POST['forced'])){
					$forced = intval($_POST['forced']);
					if($forced > 0){
						$sql = "UPDATE services SET state=?, readonly=1 WHERE idService = ?";
						$query = $db->prepare($sql);
						if($query->execute(array($forced, $idService))){
							$error = 'L\'évènement n\'a pas pu être inséré.';
						}
					}
				}
			}

			if(!isset($error)){
				$sql = "INSERT INTO events (idEvent, beginDate, endDate, typeEvent)".
						" VALUES(NULL, ?, ?, 0)";
				$query = $db->prepare($sql);
				if($query->execute(array($beginDate, $endDate))){
					$idEvent = $db->lastInsertId();
					$sql = "INSERT INTO events_isou (idEventIsou, period, isScheduled, idService, idEvent, idEventDescription)".
						" VALUES(NULL, ?, ?, ?, ?, ?)";
					$query = $db->prepare($sql);
					if($query->execute(array($period, $isScheduled, $idService, $idEvent, $idEventDescription))){
						$error = 'L\'évènement a été inséré avec succès.';
						unset($_POST);
						add_log(LOG_FILE, phpCAS::getUser(), 'INSERT', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$period.', '.$description.', '.$isScheduled.', '.$idService.')');
					}else{
						$error = 'L\'évènement n\'a pas pu être inséré.';
					}
				}else{
					$error = 'L\'évènement n\'a pas pu être inséré.';
				}
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
			$sql = "DELETE FROM events_isou WHERE idEvent = ?";
		}
		$query = $db->prepare($sql);
		if($query->execute(array($idDelEvent))){
			$sql = "DELETE FROM events ".
					" WHERE idEvent = ?";
			$query = $db->prepare($sql);
			if($query->execute(array($idDelEvent))){
				$commit = TRUE;
			}
		}

		if($commit === TRUE){
			$db->commit();
			$error = 'L\'évènement #'.$_POST['idDelEvent'].' a été supprimée avec succès.';
		}else{
			$db->rollBack();
			$error = 'L\'évènement #'.$_POST['idDelEvent'].' n\'a pas pu être supprimée.';
			add_log(LOG_FILE, phpCAS::getUser(), 'DELETE', 'Evènement #'.$_POST['idDelEvent']);
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
		$message = trim($_POST['message']);
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
				add_log(LOG_FILE, phpCAS::getUser(), 'UPDATE', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$message.', '.$description.', '.$idEvent.')');
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
					$forced = intval($_POST['forced']);
					if($forced > 0){
						$sql = "UPDATE services SET state=?, readonly=1 WHERE idService = ?";
						$query = $db->prepare($sql);
						$updateMessage = $query->execute(array($forced, $idService));
					}
				}
			}

			$sql = "UPDATE events_isou SET period=?, idEventDescription=?, isScheduled=?, idService=? WHERE idEvent=?";
			$query = $db->prepare($sql);
			$updateMessage = $updateMessage AND $query->execute(array($period, $idEventDescription, $isScheduled, $idService, $idEvent));

			if($updateMessage === TRUE){
				$error = 'L\'évènement a été mise à jour avec succès.';
				unset($_POST);
				add_log(LOG_FILE, phpCAS::getUser(), 'UPDATE', 'Evènement #'.$db->lastInsertId().' : VALUES('.$beginDate.', '.$endDate.', '.$period.', '.$description.', '.$isScheduled.', '.$idService.')');
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
