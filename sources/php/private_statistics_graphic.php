<?php

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

	$groupbySelect = array('a' => 'Tout grouper', 'm' => 'par mois');// array('a' => 'Tout grouper', 'y' => 'par année', 'm' => 'par mois', 'w' => 'par semaine');
	$typeSelect = array('Tous', 'Prévues', 'Non prévues');

	$smarty->assign('serviceSelect', $serviceSelect);
	$smarty->assign('yearSelect', $yearSelect);
	$smarty->assign('groupbySelect', $groupbySelect);
	$smarty->assign('typeSelect', $typeSelect);



if(isset($_GET['serviceSelect'], $_GET['yearSelect'], $_GET['groupbySelect'], $_GET['typeSelect'])){

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


	foreach($_GET['yearSelect'] as $key => $year){
		if(!in_array($year, $yearSelect)){
			unset($_GET['yearSelect'][$key]);
		}
	}

	if(!isset($groupbySelect[$_GET['groupbySelect']])){
		$_GET['groupbySelect'] = 'a';
	}

	if(!isset($typeSelect[$_GET['typeSelect']])){
		$_GET['typeSelect'] = 0;
	}

	$services = array();
	$calendar = array();

	foreach($_GET['yearSelect'] as $year){
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

			$sql = "SELECT S.idService, S.nameForUsers, E.beginDate, E.endDate".
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
			$sql .= " AND S.nameForUsers IS NOT NULL";

			$query = $db->query($sql);
			while($event = $query->fetch(PDO::FETCH_OBJ)){
				if($event->endDate === NULL){
					$event->endDate = TIME;
				}

				// aucun groupement
				if($_GET['groupbySelect'] === 'a'){
					if(isset($services[$event->idService])){
						$services[$event->idService]->count += $event->endDate - $event->beginDate;
					}else{
						$services[$event->idService] = new stdClass();
						$services[$event->idService]->name = $event->nameForUsers;
						$services[$event->idService]->count = $event->endDate - $event->beginDate;
					}
				}

				// groupement mensuel
				if($_GET['groupbySelect'] === 'm'){
					if(!isset($services[$event->idService])){
						$services[$event->idService] = new stdClass();
						$services[$event->idService]->name = $event->nameForUsers;
						$services[$event->idService]->month = array();
						$services[$event->idService]->count = 0;
						foreach($_GET['yearSelect'] as $y){
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
			}
		}
	}
}

if(isset($services)){
	$smarty->assign('services', $services);
}

if(isset($calendar)){
	asort($calendar);
	$smarty->assign('calendar', $calendar);
}

?>

