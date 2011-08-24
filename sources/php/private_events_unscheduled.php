<?php

	/* * * * * * * * * * * *
	 * Affichage des interruptions non prévues
	 * * * * * * * * * * * */
	$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, EI.idEventDescription, D.description, EI.isScheduled, S.idService, S.name, S.nameForUsers, S.state, S.readonly".
			" FROM events E, events_isou EI, services S, events_description D".
			" WHERE S.idService = EI.idService".
			" AND EI.idEvent = E.idEvent".
			" AND D.idEventDescription = EI.idEventDescription".
			" AND E.typeEvent = 0".
			" AND (((E.beginDate BETWEEN :0 AND :1".
			" OR E.endDate BETWEEN :2 AND :3)";
			if ($_SESSION['hide'] === 1){
				$sql .= " AND (E.endDate - E.beginDate > ".TOLERANCE."))";
				$param = array(TIMESTAMP_OF_72H_BEFORE_TODAY, TIMESTAMP_OF_LAST_CALENDAR_DAY, TIMESTAMP_OF_72H_BEFORE_TODAY, TIMESTAMP_OF_LAST_CALENDAR_DAY);
			}else{
				$sql .= ")";
				$param = array(TIMESTAMP_OF_72H_BEFORE_TODAY, TIMESTAMP_OF_LAST_CALENDAR_DAY, TIMESTAMP_OF_72H_BEFORE_TODAY, TIMESTAMP_OF_LAST_CALENDAR_DAY);
			}
			$sql .= " OR E.endDate IS NULL OR S.readonly = 1)".
			" AND S.name = 'Service final'".
			" AND S.enable = 1".
			" AND EI.isScheduled = 0".
			" ORDER BY E.beginDate DESC".
			" LIMIT 0, 50";
	$events = $db->prepare($sql);
	$events->execute($param);
	$unscheduled = array();

	while($event = $events->fetchObject()){

		// is not a scheduled event
		if($event->isScheduled == 0){
			// endDate is null
			if(empty($event->endDate)){
				$event->classCss = 'unscheduled';
			}else{
				$event->classCss = 'unscheduledfinished italic';
			}
		}

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$currentEdit = $event;
		}

		$unscheduled[] = $event;
	}

?>


