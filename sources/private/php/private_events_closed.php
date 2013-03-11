<?php

	/* * * * * * * * * * * *
	 * Affichage des services fermés
	 * * * * * * * * * * * */
	$sql = "SELECT E.idEvent, strftime('%s',E.beginDate) AS beginDate, strftime('%s',E.endDate) AS endDate, EI.period, EI.idEventDescription, D.description, EI.isScheduled, S.idService, S.name, S.nameForUsers, S.state, S.readonly".
			" FROM events E, events_isou EI, services S, events_description D".
			" WHERE S.idService = EI.idService".
			" AND EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.isScheduled = 3".
			// " AND ((E.endDate IS NOT NULL AND E.endDate > :0) OR".
			// " (E.endDate IS NULL))".
			" ORDER BY E.beginDate";
	$events = $db->prepare($sql);
	$events->execute();

	$lastIdEvent = NULL;
	$closed = array();

	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$currentEdit = $event;
		}

		if($event->idEvent === $lastIdEvent){
			$event->group = TRUE;
		}
		$lastIdEvent = $event->idEvent;

		$closed[] = $event;
	}

?>


