<?php

	/* * * * * * * * * * * *
	 * Affichage des messages
	 * * * * * * * * * * * */
	$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.shortText, EI.longText".
			" FROM events E, events_info EI".
			" WHERE EI.idEvent = E.idEvent".
			" AND E.typeEvent = 2".
			" AND ((E.endDate IS NOT NULL AND E.endDate > :0) OR".
			" (E.endDate IS NULL))".
			" ORDER BY E.beginDate";
	$events = $DB->prepare($sql);
	$events->execute(array(TIME));
	$messages = array();
	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$currentEdit = $event;
		}

		$messages[] = $event;
	}

?>


