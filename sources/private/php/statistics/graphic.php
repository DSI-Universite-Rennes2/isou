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

function computeStat(&$service, $date, $delay){
	switch($_GET['groupbySelect']){
		case 'a': $index = mktime(0,0,0,1,1,strftime('%Y', $date));break;
		case 'm': $index = mktime(0,0,0,strftime('%m', $date),1,strftime('%Y', $date));break;
		case 'd': $index = $date;break;
	}

/*	if(!isset($service->count[$index])){
		$service->count[$index] = 0;
	}*/

	$service->count[$index] += $delay;
	$service->total += $delay;
}

function monthStat(&$services, $event){
	if(!isset($services[$event->idService])){
		$services[$event->idService] = new stdClass();
		$services[$event->idService]->name = $event->nameForUsers;
		$services[$event->idService]->month = array();
		$services[$event->idService]->day = array();
		$services[$event->idService]->count = 0;
		foreach($years as $y){
			for($i=1;$i<13;$i++){
				$services[$event->idService]->month[$y.$i] = 0;
			}
		}
	}

	$beginDateMonth = intval(strftime('%m', $event->beginDate));
	$endDateMonth = intval(strftime('%m', $event->endDate));

	if($beginDateMonth === $endDateMonth){
		$services[$event->idService]->month[$year.$beginDateMonth] += $event->endDate - $event->beginDate;
		$services[$event->idService]->count += $event->endDate - $event->beginDate;
		if(!isset($calendar[$year.$beginDateMonth])){
			$calendar[$year.$beginDateMonth] = mktime(0,0,0,$beginDateMonth,1,$year);
		}
	}else{
		$beginDateYear = strftime('%Y', $event->beginDate);
		$beginDateDay = strftime('%d', mktime(0,0,0,$beginDateMonth,1,$beginDateYear)-1);
		$beginSupp = mktime(23,59,59,$beginDateMonth,$beginDateDay,$beginDateYear)-$event->beginDate;

		$endDateYear = strftime('%Y', $event->endDate);
		$endSupp = $event->endDate-mktime(0,0,0,$endDateMonth,1,$endDateYear);

		$services[$event->idService]->month[$year.$beginDateMonth] += $beginSupp;
		$services[$event->idService]->month[$year.$endDateMonth] += $endSupp;
		$services[$event->idService]->count += $beginSupp + $endSupp;

		$beginDateMonth++;
		$endDateMonth--;

		while($beginDateMonth < $endDateMonth){
			if(!isset($calendar[$year.$beginDateMonth])){
				$calendar[$year.$beginDateMonth] = mktime(0,0,0,$beginDateMonth,1,$year);
			}

			$tmpBeginDate = mktime(0,0,0,$beginDateMonth,1,$beginDateYear);
			$beginDateMonth++;
			$services[$event->idService]->month[$year.($beginDateMonth-1)] += mktime(0,0,0,$beginDateMonth,1,$beginDateYear)-$tmpBeginDate-1;
			$services[$event->idService]->count += mktime(0,0,0,$beginDateMonth,1,$beginDateYear)-$tmpBeginDate-1;
		}
	}
}

	$sql = "SELECT DISTINCT S.idService, S.nameForUsers".
			" FROM services S".
			" WHERE S.nameForUsers IS NOT NULL".
			" ORDER BY S.nameForUsers";
	$serviceSelect = array();
	if($services = $db->query($sql)){
		$serviceSelect['all'] = 'Tous les services';
		while($service = $services->fetch()){
			$serviceSelect[$service[0]] = $service[1];
		}
	}
	unset($services);

	$yearSelect = array();
	$db_path = dirname(substr(DB_PATH,7));
	if(is_dir($db_path)){
		if($dh = opendir($db_path)){
			while(($file = readdir($dh)) !== FALSE){
				if(preg_match('#isou-(\d{4})\.sqlite3#', $file, $year) > 0){
					$yearSelect[] = $year[1];
				}else if($file === 'isou.sqlite3'){
					$yearSelect[] = date('Y');
				}
			}
			closedir($dh);
		}
	}
	rsort($yearSelect);

	$groupbySelect = array('a' => 'Tout grouper', 'm' => 'par mois', 'd' => 'par jour');// array('a' => 'Tout grouper', 'y' => 'par année', 'm' => 'par mois', 'w' => 'par semaine');
	$typeSelect = array('Tous', 'Prévues', 'Non prévues');

	$smarty->assign('serviceSelect', $serviceSelect);
	$smarty->assign('yearSelect', $yearSelect);
	$smarty->assign('groupbySelect', $groupbySelect);
	$smarty->assign('typeSelect', $typeSelect);



if(isset($_GET['serviceSelect'], $_GET['beginDate'], $_GET['endDate'], $_GET['groupbySelect'], $_GET['typeSelect'])){

	$inServiceId = '';
	$keyAll = array_search('all', $_GET['serviceSelect']);
	if($keyAll !== FALSE){
		unset($_GET['serviceSelect'][$keyAll]);
	}

	foreach($_GET['serviceSelect'] as $service){
		if(!isset($serviceSelect[$service])){
			unset($serviceSelect[$service]);
		}else{
			$inServiceId .= $service.',';
		}
	}

	if(empty($inServiceId)){
		unset($inServiceId);// = 'all';
	}else{
		$inServiceId = substr($inServiceId, 0, -1);
	}

	$beginDate = strtotimestamp($_GET['beginDate']);
	if(empty($_GET['beginDate'])){
		$beginDate = TIME;
		$_GET['beginDate'] = strftime('%d/%m/%Y 00:00', TIME);
	}

	$endDate = strtotimestamp($_GET['endDate']);
	if(empty($_GET['endDate'])){
		$endDate = TIME;
		$_GET['endDate'] = strftime('%d/%m/%Y %H:%M', TIME);
	}

	$years = array();
	for($i=strftime('%Y', $beginDate);$i<=strftime('%Y', $endDate);$i++){
		if(in_array($i, $yearSelect)){
			$years[$i] = (string)$i;
		}
	}
	
	if(!isset($groupbySelect[$_GET['groupbySelect']])){
		$_GET['groupbySelect'] = 'a';
	}

	$calendar = array();
	if($_GET['groupbySelect'] === 'd'){
		$beginDateIndex = mktime(0,0,0,strftime('%m', $beginDate),strftime('%e', $beginDate),strftime('%Y', $beginDate));
		$endDateIndex = mktime(0,0,0,strftime('%m', $endDate),strftime('%e', $endDate),strftime('%Y', $endDate));
		while($beginDateIndex <= $endDateIndex){
			$calendar[$beginDateIndex] = 0;
			$beginDateIndex += 86400; // 24*60*60
		}
	}else{
		foreach($years as $year){
			switch($_GET['groupbySelect']){
				case 'a': $calendar[mktime(0,0,0,1,1,$year)] = 0;break;
				case 'm': 
					if($year === strftime('%Y', $beginDate)){
						$i = strftime('%m', $beginDate);
					}else{
						$i = 1;
					}

					if($year === strftime('%Y', $endDate)){
						$fi = strftime('%m', $endDate);
					}else{
						$fi = 12;
					}

					for($i;$i<=$fi;$i++){
						$calendar[mktime(0,0,0,$i,1,$year)] = 0;
					}
					break;
			}
		}
	}
	
	if(!isset($typeSelect[$_GET['typeSelect']])){
		$_GET['typeSelect'] = 0;
	}

	$services = array();

	foreach($years as $year){
		unset($dbname);

		if($year === date('Y')){
			$dbname = DB_PATH;
		}else{
			if(is_file(BASE.'/database/isou-'.$year.'.sqlite3')){
				$dbname = 'sqlite:'.BASE.'/database/isou-'.$year.'.sqlite3';
			}
		}

		if(isset($dbname)){
			try {
				$db = new PDO($dbname, '', '');
			} catch (PDOException $e) {
				continue;
			}

			if($_GET['typeSelect'] == '1'){
				$sql = " WHERE EI.isScheduled = 1";
			}else if($_GET['typeSelect'] == '2'){
				$sql = " WHERE EI.isScheduled = 0";
			}else{
				$sql = " WHERE EI.isScheduled < 2";
			}

			if(isset($inServiceId)){
				$sql .= " AND EI.idService IN (".$inServiceId.")";
			}

			$sql_count = "SELECT count(*) FROM events E, events_isou EI".$sql." AND E.idEvent=EI.idEvent";

			$sql = "SELECT S.idService, S.nameForUsers, strftime('%s', E.beginDate) AS beginDate, strftime('%s', E.endDate) AS endDate".
			" FROM events E, events_isou EI, services S".$sql;
			if($_GET['serviceSelect'] === 'all'){
				$sql .= " AND S.idService = EI.idService".
						" AND E.idEvent = EI.idEvent".
						" AND E.typeEvent = 0";
			}else{
				$sql .= " AND S.idService = EI.idService".
						" AND E.idEvent = EI.idEvent".
						" AND E.typeEvent = 0";
			}
			$sql .= " AND S.nameForUsers IS NOT NULL".
					" AND strftime('%s', E.beginDate) >= ?".
					" AND (strftime('%s', E.endDate) <= ?)";// AND E.endDate NOT NULL)";
			// TODO: mettre en place une meilleure gestion des évènements commencés avant beginDate

			$query = $db->prepare($sql);
			$query->execute(array($beginDate, $endDate));

			while($event = $query->fetch(PDO::FETCH_OBJ)){
				if($event->endDate === NULL){
					$event->endDate = TIME;
				}

				if(!isset($services[$event->idService])){
					// init services
					$services[$event->idService] = new stdClass();
					$services[$event->idService]->name = $event->nameForUsers;
					$services[$event->idService]->events = $calendar;// array();
					$services[$event->idService]->count = $calendar;
					$services[$event->idService]->total = 0;
					
					// init days
					$beginDateIndex = mktime(0,0,0,strftime('%m', $beginDate),strftime('%e', $beginDate),strftime('%Y', $beginDate));
					$endDateIndex = mktime(0,0,0,strftime('%m', $endDate),strftime('%e', $endDate),strftime('%Y', $endDate));
					while($beginDateIndex <= $endDateIndex){
						$services[$event->idService]->events[$beginDateIndex] = 0;
						$beginDateIndex += 86400; // 24*60*60
					}
				}

				// calcul par journée
				$beginDateMonth = intval(strftime('%m', $event->beginDate));
				$endDateMonth = intval(strftime('%m', $event->endDate));
				$beginDateDay = intval(strftime('%e', $event->beginDate));
				$endDateDay = intval(strftime('%e', $event->endDate));
				if($beginDateMonth === $endDateMonth && $beginDateDay === $endDateDay){
					// most simple case
					$dateIndex = mktime(0,0,0,$beginDateMonth,$beginDateDay, $year);
					$services[$event->idService]->events[$dateIndex] += $event->endDate - $event->beginDate;
					computeStat($services[$event->idService], $dateIndex, $event->endDate - $event->beginDate);
				}else{
					$oldBeginDateDay = $event->beginDate;
					$event->beginDate = mktime(23,59,59,$beginDateMonth,$beginDateDay,$year)+1;
					$dateIndex = mktime(0,0,0,$beginDateMonth,$beginDateDay, $year);
					$services[$event->idService]->events[$dateIndex] += $event->beginDate - $oldBeginDateDay;
					computeStat($services[$event->idService], $dateIndex, $event->beginDate - $oldBeginDateDay);
					$beginDateDay = intval(strftime('%e', $event->beginDate));

					$oldEndDateEvent = $event->endDate;
					$event->endDate = mktime(0,0,0,$endDateMonth,$endDateDay,$year)-1;
					$dateIndex = mktime(0,0,0,$endDateMonth,$endDateDay, $year);
					$services[$event->idService]->events[$dateIndex] += $oldEndDateEvent - $event->endDate;
					computeStat($services[$event->idService], $dateIndex, $oldEndDateEvent - $event->endDate);
					$endDateDay = intval(strftime('%e', $event->endDate));

					while($event->beginDate < $event->endDate){
						$services[$event->idService]->events[$event->beginDate] += 86400; // 24*60*60
						computeStat($services[$event->idService], $event->idService, 86400);
						$event->beginDate += 86400; // 24*60*60
						/*
						   RETODO
						if(!isset($calendar[$year.$beginDateMonth])){
							$calendar[$year.$beginDateMonth] = mktime(0,0,0,$beginDateMonth,1,$year);
						}
						*/
					}
				}
			}
		}
	}
}

if(isset($services)){
	$smarty->assign('services', $services);
	$smarty->assign('calendar', $calendar);
}

?>

