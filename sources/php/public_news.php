<?php

function get_parents($idChild){
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
		add_log(LOG_FILE, phpCAS::getUser(), 'ERROR_DB', $e->getMessage());
	}

	// close pdo connection
	$dbr = null;

	return $parents;
}

if(class_exists('IsouService') === FALSE){
	// used by news page
	require BASE.'/classes/isou/isou_service.class.php';
	require BASE.'/classes/isou/isou_event.class.php';

	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>';
	$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_news.js"></script>';
	$title = NAME.' - Actualité';
	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/news.css" media="screen" />';

	// 2 jours avant
	$BEFORE = mktime(0,0,0)-(48*60*60);
	// 2 jours apres
	$AFTER = mktime(0,0,0)+(48*60*60);
}else{
	// used by calendar page
	$BEFORE = TIMESTAMP_OF_FIRST_CALENDAR_DAY;
	$AFTER = TIMESTAMP_OF_LAST_CALENDAR_DAY;
	$calendar = TRUE;
}

$sql = "SELECT EI.shortText, EI.longText".
		" FROM events_info EI, events E".
		" WHERE E.idEvent = EI.idEvent".
		" AND (E.beginDate > ?".
		" OR E.endDate IS NULL)";
$query = $db->prepare($sql);
$messages = array();
if($query->execute(array($BEFORE))){
	while($message = $query->fetchObject()){
		$messages[] = $message;
	}
}

$services = array();

// recupere tous les services dans la bdd qui ont un évènement entre le lundi de la semaine d'avant, et 35 jours après ce lundi
$sql = "SELECT DISTINCT S.idService, S.nameForUsers, S.url, S.state, C.name".
	" FROM categories C, services S, events E, events_isou EI".
	" WHERE C.idCategory = S.idCategory".
	" AND EI.idEvent = E.idEvent".
	" AND E.typeEvent = 0".
	" AND S.idService = EI.idService".
	" AND S.enable = 1".
	" AND S.visible = 1".
	" ORDER BY C.position, UPPER(S.nameForUsers)";
$i=0;
$categoryName = '';
$categories = array();
if($service_records = $db->query($sql)){
	$tab_record = $service_records->fetchAll();
	$r = 0;
	while(isset($tab_record[$r][0])){
		$record = $tab_record[$r];
		$r++;
		//construct($id, $name = NULL, $nameForUsers = NULL, $url = NULL, $state = 3, $comment = NULL, $categoryName = NULL)
		$services[$i] = new IsouService($record[0], NULL, $record[1], $record[2], $record[3], NULL, $record[4]);
		// recupere tous les evenements par services dans la bdd
		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, D.description, EI.period, EI.isScheduled".
				" FROM events E, events_isou EI, events_description D".
				" WHERE EI.idService = :0".
				" AND EI.idEventDescription = D.idEventDescription".
				" AND EI.idEvent = E.idEvent".
				" AND E.typeEvent = 0".
				// n'afficher que les interruptions régulières si elles ont lieu en ce moment
				" AND ((EI.isScheduled = 2 AND E.beginDate <= :1 AND E.endDate >= :2)";
				if(isset($calendar)){
				$sql .= " OR (EI.isScheduled = 1";
				}else{
				$sql .= " OR (EI.isScheduled < 2";
				}
				$sql .= " AND (E.endDate IS NULL OR".
				" ((E.beginDate BETWEEN :3 AND :4".
				" OR E.endDate BETWEEN :5 AND :6)";
				if($_SESSION['hide'] === 1 || $IS_ADMIN === FALSE){
					$sql .= " AND (E.endDate-E.beginDate > ".TOLERANCE.")";
					$param = array($record[0], TIME, TIME, $BEFORE, $AFTER, $BEFORE, $AFTER);
				}else{
					$param = array($record[0], TIME, TIME, $BEFORE, $AFTER, $BEFORE, $AFTER);
				}
				$sql .= " )))) ORDER BY E.beginDate DESC";

		// ajoute les évènements
		$event_records = $db->prepare($sql);

		if($event_records->execute($param)){
			$e = 0;
			$event_record = $event_records->fetchAll();

			while(isset($event_record[$e][0]) && $e < 10){
				$nagiosEvents = array();
				if(!empty($event_record[$e][2])){
					// si l'evenement n'est pas en cours, on recup tous les evenements nagios
					// on met +/- 10 secondes de tolérence
					$sql = "SELECT DISTINCT s.name, s.nameForUsers, e.beginDate, e.endDate, en.state".
							" FROM events e, events_nagios en, services s, dependencies d".
							" WHERE s.idService = en.idService".
							" AND e.idEvent = en.idEvent".
							" AND d.idServiceParent = s.idService".
							" AND d.idService = :0".
							" AND e.beginDate >= :1".
							" AND e.endDate <= :2";

					$nagiosevent_records = $db->prepare($sql);
					if($nagiosevent_records->execute(array($record[0], $event_record[$e][1]-TOLERANCE, $event_record[$e][2]+TOLERANCE))){
						$j = 0;
						while($nagiosevent_record = $nagiosevent_records->fetch()){
							if(empty($nagiosevent_record[1])){
								$nagiosevent_record[1] = $nagiosevent_record[0];
							}
							// $id,$beginDate,$endDate,$period,$description,$serviceName,$state = NULL, $isScheduled = NULL, $type = NULL, $nagiosEvents = NULL
							$nagiosEvents[$j] = new IsouEvent($event_record[$e][0],$nagiosevent_record[2],$nagiosevent_record[3],NULL,NULL,$nagiosevent_record[1],$nagiosevent_record[4]);
							$j++;
						}
					}
				}
				$services[$i]->setEvent(new IsouEvent($event_record[$e][0],$event_record[$e][1],$event_record[$e][2],$event_record[$e][4],$event_record[$e][3],$services[$i]->getNameForUsers(),$services[$i]->getState(), $event_record[$e][5], NULL, $nagiosEvents));
				$e++;
			}
		}

		if($services[$i]->hasEvents()){
			if($categoryName !== $record[4]){
				$categoryName = $record[4];
				$categories[] = new stdClass();
				$categories[count($categories)-1]->name = $record[4];
				$categories[count($categories)-1]->services = array();
			}
			$categoriesService = new stdClass();
			$categoriesService->flag = $services[$i]->getState();
			$categoriesService->stripName = strip_accents($services[$i]->getNameForUsers());
			$categoriesService->state = $services[$i]->getState();
			$categoriesService->url = $services[$i]->getUrl();
			$categoriesService->name = $services[$i]->getNameForUsers();
			$categoriesService->id = $services[$i]->getId();
			$categoriesService->parents = get_parents($categoriesService->id);
			foreach($services[$i]->getEvents() as $event){
				$serviceEvent = new stdClass();
				$serviceEvent->beginDate = $event->getBeginDate();
				$serviceEvent->endDate = $event->getEndDate();
				$serviceEvent->description = $event->getDescription();
				$serviceEvent->message = $event->Message(false, true);
				$serviceEvent->scheduled = $event->getScheduled();
				$serviceEvent->period = $event->getPeriod();
				$nagiosEvents = $event->getNagiosEvents();
				if(count($nagiosEvents) > 0 && $IS_ADMIN === TRUE){
					$serviceEvent->nagios[] = new stdClass();
					$k = count($serviceEvent->nagios)-1;
					foreach($nagiosEvents as $nagiosEvent){
						$serviceEvent->nagios[$k]->state = $nagiosEvent->getState();
						$serviceEvent->nagios[$k]->name = $nagiosEvent->getServiceName();
						$serviceEvent->nagios[$k]->beginDate = $nagiosEvent->getBeginDate();
						$serviceEvent->nagios[$k]->endDate = $nagiosEvent->getEndDate();
					}
				}
				$categoriesService->events[] = $serviceEvent;
			}
			$categories[count($categories)-1]->services[] = $categoriesService;
			$i++;
		}else{
			unset($services[$i]);
		}
	}
}

$smarty->assign('categories', $categories);
$smarty->assign('messages', $messages);

?>
