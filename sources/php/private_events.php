<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/events.css" media="screen">'.
			'<link rel="stylesheet" type="text/css" media="all" href="'.URL.'/css/private_events_calendar.css"  />';

	/*$script = '<script type="text/javascript" src="'.URL.'/js/jscalendar-1.0/calendar.js"></script>
				<script type="text/javascript" src="'.URL.'/js/jscalendar-1.0/lang/calendar-fr.js"></script>
				<script type="text/javascript" src="'.URL.'/js/jscalendar-1.0/calendar-setup.js"></script>
				<script type="text/javascript" src="'.URL.'/js/jscalendar-1.0.js"></script>
				<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>
				<script type="text/javascript" src="'.URL.'/js/jquery_events.js"></script>';*/
	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>'.
				'<script type="text/javascript" src="'.URL.'/js/jquery.dynDateTime.min.js"></script>'.
				'<script type="text/javascript" src="'.URL.'/js/jquery.dynDateTime-fr.min.js"></script>'.
				'<script type="text/javascript" src="'.URL.'/js/jquery_events.js"></script>';

	$title = NAME.' - Administration des Evènements';

	$p=0;
	$date = getdate();

	define('TIMESTAMP_OF_LAST_CALENDAR_DAY', ((mktime(0,0,0)-(6+$date["wday"])*24*60*60)+35*24*60*60));
	define('TIMESTAMP_OF_72H_BEFORE_TODAY', mktime(0,0,0)-3*24*60*60);

if(count($_POST) > 0){
	require BASE.'/php/private_events.formsprocess.php';
}

	require BASE.'/php/private_events.elementsforms.php';

	/* * * * * * * * * * *
	 * Données formulaire
	 * * * * * * * * * * */

	if(isset($_POST['scheduled']) && $_POST['scheduled']==2){
		$scheduledRadio2 = 'checked="checked" ';
		$scheduledRadio1 = '';
	}else{
		$scheduledRadio1 = 'checked="checked" ';
		$scheduledRadio2 = '';
	}

	(isset($_POST['name']))?$name=$_POST['name']:$name=0;

	(isset($_POST['beginDate']))?$beginDate=$_POST['beginDate']:$beginDate=strftime('%d/%m/%Y %H:%M',TIME);

	(isset($_POST['endDate']))?$endDate=$_POST['endDate']:$endDate=strftime('%d/%m/%Y %H:%M',TIME);

	if(isset($_POST['period']) && $_POST['period']=='weekly'){
		$periodRadio2 = 'checked="checked" ';
		$periodRadio1 = '';
	}else{
		$periodRadio1 = 'checked="checked" ';
		$periodRadio2 = '';
	}

	(isset($_POST['description']))?$description=$_POST['description']:$description='';


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
			$sql .= " OR E.endDate IS NULL)".
			" AND S.name = 'Service final'".
			" AND S.enable = 1".
			" AND S.visible = 1".
			" AND EI.isScheduled = 0".
			" ORDER BY E.beginDate DESC";
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

	/* * * * * * * * * * * *
	 * Affichage des interruptions prévues
	 * * * * * * * * * * * */
	$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, EI.idEventDescription, D.description, EI.isScheduled, S.idService, S.name, S.nameForUsers, S.state, S.readonly".
			" FROM events E, events_isou EI, services S, events_description D".
			" WHERE S.idService = EI.idService".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND EI.idEvent = E.idEvent".
			" AND E.typeEvent = 0".
			" AND (E.beginDate BETWEEN :0 AND :1".
			" OR E.endDate BETWEEN :2 AND :3".
			" OR E.endDate IS NULL)".
			" AND S.name = 'Service final'".
			" AND S.enable = 1".
			" AND S.visible = 1".
			" AND EI.isScheduled = 1".
			" ORDER BY E.beginDate DESC";

	$events = $db->prepare($sql);
	$events->execute(array(TIMESTAMP_OF_72H_BEFORE_TODAY, TIMESTAMP_OF_LAST_CALENDAR_DAY, TIMESTAMP_OF_72H_BEFORE_TODAY, TIMESTAMP_OF_LAST_CALENDAR_DAY));
	$scheduled = array();

	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$currentEdit = $event;
		}

		$scheduled[] = $event;
	}

	/* * * * * * * * * * * *
	 * Affichage des interruptions régulières
	 * * * * * * * * * * * */

	$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, EI.idEventDescription, D.description, EI.isScheduled, S.idService, S.name, S.nameForUsers, S.state, S.readonly".
			" FROM events E, events_isou EI, services S, events_description D".
			" WHERE S.idService = EI.idService".
			" AND EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.isScheduled = 2".
			" ORDER BY E.beginDate";
	$events = $db->query($sql);
	$regular = array();

	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$currentEdit = $event;
		}

		$regular[] = $event;
	}


	/* * * * * * * * * * * *
	 * Affichage des services fermés
	 * * * * * * * * * * * */
	$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, EI.idEventDescription, D.description, EI.isScheduled, S.idService, S.name, S.nameForUsers, S.state, S.readonly".
			" FROM events E, events_isou EI, services S, events_description D".
			" WHERE S.idService = EI.idService".
			" AND EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.isScheduled = 3".
			" AND ((E.endDate IS NOT NULL AND E.endDate > :0) OR".
			" (E.endDate IS NULL))".
			" ORDER BY E.beginDate";
	$events = $db->prepare($sql);
	$events->execute(array(TIME));
	$closed = array();
	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$currentEdit = $event;
		}

		$closed[] = $event;
	}


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
	$events = $db->prepare($sql);
	$events->execute(array(TIME));
	$messages = array();
	while($event = $events->fetchObject()){

		if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
			(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
			$event->edit = TRUE;
			$event->shortText = $event->shortText;
			$event->longText = $event->longText;
			$currentEdit = $event;
		}else{
			$event->shortText = htmlentities($event->shortText, ENT_QUOTES);
			$event->longText = htmlentities($event->longText, ENT_QUOTES);
		}

		$messages[] = $event;
	}

	$smarty->assign('unscheduled', $unscheduled);
	$smarty->assign('scheduled', $scheduled);
	$smarty->assign('regular', $regular);
	$smarty->assign('closed', $closed);
	$smarty->assign('messages', $messages);
	$smarty->assign('optionNameForUsers', $optionNameForUsers);
	if(isset($currentEdit)){
		$smarty->assign('currentEdit', $currentEdit);
	}

?>


