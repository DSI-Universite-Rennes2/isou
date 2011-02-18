<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/table.css" media="screen" />';
	$css .= '<link rel="stylesheet" type="text/css" href="'.URL.'/css/contact.css" media="screen" />';
	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>';
	$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_table.js"></script>';

	$title = NAME.' - Liste';

	// 2 jours avant
	$BEFORE = mktime(0,0,0)-(48*60*60);

	require BASE.'/classes/isou/isou_service.class.php';
	require BASE.'/classes/isou/isou_event.class.php';

	$services = array();
	$categories = array();
	$service_options[0] = '&nbsp;';

	$sql = "SELECT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, C.name".
			" FROM services S, categories C".
			" WHERE C.idCategory = S.idCategory".
			" AND S.enable = 1".
			" AND S.visible = 1".
			" ORDER BY C.position, UPPER(S.nameForUsers)";
	if($service_records = $db->query($sql)){
		$service_records = $service_records->fetchAll();
		$i = 0;
		$categoryName = '';
		while(isset($service_records[$i][0])){
			$services[$i] = new IsouService($service_records[$i][0], $service_records[$i][1], $service_records[$i][2], $service_records[$i][3], $service_records[$i][4], $service_records[$i][5], $service_records[$i][6]);
			if($service_records[$i][4] == 4){
				// service ferme
				$sql = "SELECT E.idEvent, E.beginDate, E.endDate, D.description".
					" FROM events E, events_isou EI, events_description D".
					" WHERE EI.idEvent = E.idEvent".
					" AND EI.idEventDescription = D.idEventDescription".
					" AND E.typeEvent = 0".
					" AND EI.idService = :0".
					" AND EI.isScheduled = 3".
					" AND (E.endDate >= :1 OR E.endDate IS NULL)".
					" ORDER BY E.beginDate";
				$event_records = $db->prepare($sql);
				if($event_records->execute(array($service_records[$i][0], TIME))){
					$event = $event_records->fetch();
					if(isset($event[0])){
						$services[$i]->setEvent(new IsouEvent($event[0],$event[1],$event[2],NULL,$event[3], NULL, NULL, NULL,'LastInterruption'));
					}
				}
			}else{
				// Interruptions régulières
				$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description".
					" FROM events E, events_isou EI, events_description D".
					" WHERE EI.idEvent = E.idEvent".
					" AND EI.idEventDescription = D.idEventDescription".
					" AND E.typeEvent = 0".
					" AND EI.idService = :0".
					" AND EI.isScheduled = 2".
					" ORDER BY E.beginDate";
				$event_records = $db->prepare($sql);
				if($event_records->execute(array($service_records[$i][0]))){
					while($event = $event_records->fetch()){
						$services[$i]->setEvent(new IsouEvent($event[0],$event[1],$event[2],$event[3],$event[4], NULL, NULL, 2, 'RegularInterruptions'));
					}
				}

				// derniere interruption (48 heures plutot)
				$sql = "SELECT E.idEvent, E.beginDate, E.endDate, D.description".
					" FROM events E, events_isou EI, events_description D".
					" WHERE EI.idEvent = E.idEvent".
					" AND EI.idEventDescription = D.idEventDescription".
					" AND E.typeEvent = 0".
					" AND EI.idService = :0".
					" AND ((EI.isScheduled < 2".
					" AND (E.endDate BETWEEN :1 AND :2 OR E.endDate IS NULL)".
					" AND (E.endDate - E.beginDate > ".TOLERANCE." OR E.endDate IS NULL))".
					" OR (EI.isScheduled = 3".
					" AND E.beginDate < :3".
					" AND (E.endDate > :4".
					" OR E.endDate IS NULL)))".
					" ORDER BY E.beginDate DESC";
				$event_records = $db->prepare($sql);
				if($event_records->execute(array($service_records[$i][0], $BEFORE, TIME, TIME, TIME))){
					$event = $event_records->fetch();
					if(isset($event[0])){
						$services[$i]->setEvent(new IsouEvent($event[0],$event[1],$event[2],NULL,$event[3], NULL, NULL, NULL,'LastInterruption'));
					}
				}


				// prochaine interruption
				$sql = "SELECT E.idEvent, E.beginDate, E.endDate, D.description".
				" FROM events E, events_isou EI, events_description D".
				" WHERE EI.idEvent = E.idEvent".
				" AND EI.idEventDescription = D.idEventDescription".
				" AND E.typeEvent = 0".
				" AND EI.idService = :0".
				" AND E.beginDate > :1".
				" AND EI.isScheduled < 2".
				" ORDER BY E.beginDate";
				$event_records = $db->prepare($sql);
				if($event_records->execute(array($service_records[$i][0], TIME))){
					$event = $event_records->fetch();
					if(isset($event[0])){
						$services[$i]->setEvent(new IsouEvent($event[0],$event[1],$event[2],NULL,$event[3], NULL, NULL, NULL,'NextInterruption'));
					}
				}
			}

			$service_options[$service_records[$i][0]] = $service_records[$i][2];

			if($categoryName !== $service_records[$i][6]){
				$categoryName = $service_records[$i][6];
				$categories[] = new stdClass();
				$categories[count($categories)-1]->name = $service_records[$i][6];
				$categories[count($categories)-1]->services = array();
			}
			$categoriesService = new stdClass();
			$categoriesService->state = $services[$i]->getState();
			$categoriesService->name = $services[$i]->getNameForUsers();
			$categoriesService->url = $services[$i]->getUrl();
			$categoriesService->comment = $services[$i]->getComment();
			$categoriesService->closed = $services[$i]->isClosed();
			if($categoriesService->closed === TRUE){
				$categoriesService->beginDate = $event[1];
				$categoriesService->endDate = $event[2];
				$categoriesService->reason = $event[3];
			}else{
				$lastEvent = $services[$i]->getLastInterruption();
				$categoriesService->beginDateLastEvent = $lastEvent[0];
				$categoriesService->endDateLastEvent = $lastEvent[1];
				$categoriesService->reasonLastEvent = $lastEvent[2];
				$categoriesService->nextEvent = $services[$i]->getNextEvent();
				$categoriesService->regularInterruption = $services[$i]->getRegularInterruption();
			}

			$categories[count($categories)-1]->services[] = $categoriesService;

			$i++;
		}
	}

	$smarty->assign('categories', $categories);

?>

