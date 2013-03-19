<?php

	/* * * * * * * * * * * *
	 * Affichage des interruptions régulières
	 * * * * * * * * * * * */

	$sql = "SELECT E.idEvent, strftime('%s',E.beginDate) AS beginDate, strftime('%s',E.endDate) AS endDate, EI.period, EI.idEventDescription, D.description, EI.isScheduled, S.idService, S.name, S.nameForUsers, S.state, S.readonly".
			" FROM events E, events_isou EI, services S, events_description D".
			" WHERE S.idService = EI.idService".
			" AND EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.isScheduled = 2".
			" ORDER BY E.beginDate";
	$events = $db->query($sql);

	$lastIdEvent = NULL;
	$regular = array();

	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			if($event->period == '604800'){
				$event->strperiod = 'weekly';
			}else{
				$event->strperiod = 'daily';
			}
			$currentEdit = $event;
		}

		$event->beginDate = gmstrftime('%Y-%m-%dT%H:%M', $event->beginDate);
		if($event->endDate !== NULL){
			$event->endDate = gmstrftime('%Y-%m-%dT%H:%M', $event->endDate);
		}

		if($event->idEvent === $lastIdEvent){
			$event->group = TRUE;
		}
		$lastIdEvent = $event->idEvent;

		$regular[] = $event;
	}

?>


