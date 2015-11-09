<?php

	$TITLE = NAME.' - Journal';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_record.js');

	// 2 jours avant
	$BEFORE = strftime('%Y-%m-%dT%H:%M', mktime(0,0,0)-(48*60*60));
	// 2 jours apres
	$AFTER = strftime('%Y-%m-%dT%H:%M', mktime(0,0,0)+(48*60*60));

// Fonction de comparaison
function cmp($a, $b) {
	if($a->endDate === $b->endDate){
		if($a->beginDate === $b->beginDate){
			if($a->nameForUsers === $b->nameForUsers){
				return 0;
			}else{
				if(isset($a->nameForUsers)){
					if(isset($b->nameForUsers)){
						return ($a->nameForUsers > $b->nameForUsers) ? 1 : -1;
					}else{
						return 1;
					}
				}else{
					return 1;
				}
			}
		}else{
			return ($a->beginDate < $b->beginDate) ? 1 : -1;
		}
	}
	return ($a->endDate < $b->endDate) ? 1 : -1;
}


$sql = "SELECT E.idEvent, strftime('%s', E.beginDate) AS beginDate, strftime('%s', E.endDate) AS endDate, E.typeEvent".
		" FROM events E".
		" WHERE E.endDate IS NULL".
		" OR (E.endDate > :0".
		" AND strftime('%s', E.endDate)-strftime('%s', E.beginDate) > ".$CFG['tolerance'].
		" ) AND E.typeEvent != 1";
$param = array($BEFORE);

$events = array();
$service_records = $DB->prepare($sql);
if($service_records->execute($param)){
	while($service_record = $service_records->fetchObject()){
		if($service_record->endDate === NULL){
			$iTab = mktime(0,0,0);
		}else{
			$iTab = getdate($service_record->endDate);
			$iTab = mktime(0,0,0,$iTab["mon"],$iTab["mday"],$iTab["year"]);
		}

		if(!isset($events[$iTab])){
			$events[$iTab] = array();
		}

		$sql = '';
		$event = NULL;
		if($service_record->typeEvent === '2'){
			// message informatif
			$sql = "SELECT idEvent, shortText, longText FROM events_info WHERE idEvent = :0";
			$query = $DB->prepare($sql);
			$query->execute(array($service_record->idEvent));
			if($event = $query->fetchObject()){
				$event->typeEvent = 2;
				$event->beginDate = $service_record->beginDate;
				$event->endDate = $service_record->endDate;
				$event->shortText = nl2br(htmlentities($event->shortText, ENT_QUOTES));
				$event->longText = nl2br(htmlentities($event->longText, ENT_QUOTES));
				$event->class = "messages";
				if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
					(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
					$currentEdit = $event;
				}
			}
		}elseif($service_record->typeEvent === '0'){
			// evenement isou
			$sql = "SELECT EI.idEvent, S.idService, S.nameForUsers, S.url, S.comment, EI.period, EI.isScheduled, D.description".
					" FROM events_isou EI, services S, events_description D".
					" WHERE S.idService = EI.idService".
					" AND EI.idEventDescription = D.idEventDescription".
					" AND EI.idEvent = :0";
			$query = $DB->prepare($sql);
			$query->execute(array($service_record->idEvent));

			if($event = $query->fetchObject()){
				$event->typeEvent = 0;
				$event->beginDate = gmstrftime('%Y-%m-%dT%H:%M', $service_record->beginDate);
				if($service_record->endDate === NULL){
					$event->endDate = NULL;
				}else{
					$event->endDate = gmstrftime('%Y-%m-%dT%H:%M', $service_record->endDate);
				}

				// 0 for unscheduled events, 1 for scheduled events, 2 for regular events, 3 for closed services
				switch($event->isScheduled){
					case '0': $event->class = "unscheduled";break;
					case '1': $event->class = "scheduled";break;
					case '2': $event->class = "regular";$event->flag = -1;break;
					case '3': $event->class = "closed";$event->flag = 4;break;
					default: $event->class = "unscheduled";break;
				}

				if($event->isScheduled !== '3'){
					$sql = "SELECT MAX(n.state) AS state".
							" FROM events E, events_nagios N, services s, dependencies d".
							" WHERE s.idService = N.idService".
							" AND E.idEvent = N.idEvent".
							" AND E.typeEvent = 1".
							" AND d.idServiceParent = s.idService".
							" AND d.idService = ?".
							" AND E.beginDate >= ?".
							" AND (E.endDate <= ?".
							" OR E.endDate IS NULL)";
					$query = $DB->prepare($sql);
					$query->execute(array($event->idService, $event->beginDate, $event->endDate));
					if($state = $query->fetch(PDO::FETCH_NUM)){
						$event->state = $state[0];
					}

					if($event->state === NULL){
						$event->state = 2;
					}
				}

				if((isset($_GET['modify']) && $_GET['modify'] == $event->idEvent) ||
					(isset($_POST['idEvent']) && $_POST['idEvent'] == $event->idEvent)){
					$currentEdit = $event;
				}
			}
		}

		if(isset($event->beginDate)){
			$events[$iTab][] = $event;
		}
	}
}

krsort($events);
foreach($events as $key => $event){
	usort($event, 'cmp');
	$events[$key] = $event;
}

$smarty->assign('events', $events);

if(isset($currentEdit)){
	$smarty->assign('currentEdit', $currentEdit);
}

if($IS_ADMIN === TRUE){
	require PRIVATE_PATH.'/php/events/elementsforms.php';
}

$template = 'public/journal.tpl';

?>
