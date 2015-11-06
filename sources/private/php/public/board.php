<?php

	$TITLE = NAME.' - Tableau';

	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/board.css');

	// 2 jours avant
	$BEFORE = mktime(0,0,0)-(48*60*60);

	require PRIVATE_PATH.'/classes/isou/isou_service.class.php';
	require PRIVATE_PATH.'/classes/isou/isou_event.class.php';

	$services = array();
	$categories = array();
	$service_options[0] = '&nbsp;';

	$sql = "SELECT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, S.readonly, C.name".
			" FROM services S, categories C".
			" WHERE C.idCategory = S.idCategory".
			" AND S.enable = 1".
			" AND S.visible = 1".
			" ORDER BY C.position, UPPER(S.nameForUsers)";
	if($service_records = $db->query($sql)){
		$service_records = $service_records->fetchAll();
		function getDependencies($idService, $date, $events, $db){
			$sql = "SELECT E.beginDate, E.endDate, 4 AS state, (E.endDate-E.beginDate) AS total, EI.idService".
					" FROM events E, events_isou EI".
					" WHERE EI.idService = :0".
					" AND EI.idEvent = E.idEvent".
					" AND EI.isScheduled = 3".
					" AND ((E.endDate >= :1".
					" AND E.beginDate <= :2)".
					" OR E.endDate IS NULL)";
			$parents = $db->prepare($sql);
			$parents->execute(array($idService));
			if($parent = $parents->fetch(PDO::FETCH_OBJ)){
				return array($parent);
			}

			$sql = "SELECT idServiceParent".
					" FROM dependencies".
					" WHERE idService = :0";
			$parents = $db->prepare($sql);
			$parents->execute(array($idService));
			while($parent = $parents->fetch(PDO::FETCH_OBJ)){
				$events = getDependencies($parent->idServiceParent, $date, $events, $db);
				$sql = "SELECT E.beginDate, E.endDate, EN.state, (E.endDate-E.beginDate) AS total, EN.idService".
						" FROM events E, events_nagios EN".
						" WHERE EN.idService = :0".
						" AND EN.idEvent = E.idEvent".
						" AND ((E.endDate >= :1".
						" AND E.beginDate <= :2)".
						" OR E.endDate IS NULL)";
				$query = $db->prepare($sql);
				$query->execute(array($parent->idServiceParent, $date, $date+(24*60*60)));
				while($event = $query->fetch(PDO::FETCH_OBJ)){

					if($event->endDate === NULL){
						$event->endDate = TIME;
					}

					if($event->endDate > $date+(24*60*60)){
						$event->endDate = $date+(24*60*60);
					}

					if($event->beginDate < $date){
						$event->beginDate = $date;
					}

					$needUpdate = TRUE;
					foreach($events as $item){
						if($item->beginDate >= $event->beginDate && $item->beginDate <= $event->endDate){
							$item->beginDate = $event->beginDate;
							$needUpdate = FALSE;
						}

						if($item->endDate <= $event->endDate && $item->endDate >= $event->beginDate){
							$item->endDate = $event->endDate;
							$needUpdate = FALSE;
						}

						if($event->state < $item->state){
							$item->state = $event->state;
						}
					}

					if($needUpdate === TRUE){
						$events[] = $event;
					}
				}
			}
			return $events;
		}

		$i = 0;
		$op = 0;
		$categoryName = '';

		while(isset($service_records[$i][0])){
			$op++;
			$services[$i] = new IsouService($service_records[$i][0], $service_records[$i][1], $service_records[$i][2], $service_records[$i][3], $service_records[$i][4], $service_records[$i][5], $service_records[$i][6], $service_records[$i][7]);

			$days = array();
			for($d=0;$d<7;$d++){
				$today = mktime(0,0,0)-(24*60*60*$d);
				$sql = "SELECT newStateForChild".
						" FROM dependencies D, events_nagios EN, events E".
						" WHERE E.idEvent = EN.idEvent".
						" AND D.idServiceParent = EN.idService".
						" AND EN.state = D.stateOfParent".
						" AND (E.beginDate BETWEEN :1 AND :2".
						" OR E.endDate BETWEEN :3 AND :4 AND E.endDate-E.beginDate > 10*60)".
						" AND D.idService = :0";

				$days[$d] = new stdClass();
				$days[$d]->interrupt = 0;

				$sql = "SELECT MAX(newStateForChild) AS state".
						" FROM dependencies D, events_nagios EN, events E".
						" WHERE E.idEvent = EN.idEvent".
						" AND D.idServiceParent = EN.idService".
						" AND EN.state = D.stateOfParent".
						" AND D.idService = :0".
						" AND ((E.endDate >= :1".
						" AND E.beginDate <= :2 AND E.endDate-E.beginDate > 10*60)".
						" OR E.endDate IS NULL)";
				$query = $db->prepare($sql);

				$query->execute(array($service_records[$i][0], $today, ($today+(24*60*60))));

				$dependencie = $query->fetch(PDO::FETCH_OBJ);
				if($dependencie === FALSE || $dependencie->state === NULL){
					$days[$d]->state = 0; // $GLOBALS['FLAGS'][0];
				}else{
					$days[$d]->state = $dependencie->state;
				}

				$sql = "SELECT EI.idEvent".
						" FROM events_isou EI, events E".
						" WHERE E.idEvent = EI.idEvent".
						" AND EI.isScheduled = 3".
						" AND EI.idService = :0".
						" AND ((E.endDate >= :1".
						" AND E.beginDate <= :2)".
						" OR E.endDate IS NULL)";
				$query = $db->prepare($sql);

				$query->execute(array($service_records[$i][0], $today, ($today+(24*60*60))));
				if($closed = $query->fetch(PDO::FETCH_OBJ)){
					$days[$d]->state = 4;
				}
			}

			$service_options[$service_records[$i][0]] = $service_records[$i][2];

			if($categoryName !== $service_records[$i][5]){
				$categoryName = $service_records[$i][5];
				$categories[] = new stdClass();
				$categories[count($categories)-1]->name = $service_records[$i][5];
				$categories[count($categories)-1]->services = array();
			}
			$categoriesService = new stdClass();
			$categoriesService->name = $services[$i]->getNameForUsers();
			$categoriesService->days = $days;
			$categoriesService->now = $services[$i]->getState();
			$categories[count($categories)-1]->services[] = $categoriesService;

			$i++;
		}
	}

	$smarty->assign('categories', $categories);

	$template = 'public/board.tpl';

?>

