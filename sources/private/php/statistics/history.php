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

	$monthSelect = array('');
	for($i = 1;$i<13;$i++){
		$monthSelect[] = strftime('%B',mktime(0, 0, 0, $i));
	}

	$beginSort = array('Décroissant', 'Croissant');
	$endSort = array('Décroissant', 'Croissant');
	$typeSelect = array('Tous', 'Prévues', 'Non prévues');

	$maxResultSelect = array();
	for($i = 10;$i<101;$i = $i + 10){
		$maxResultSelect[] = $i;
	}
	$maxResultSelect[-1] = 'illimité';

	$smarty->assign('serviceSelect', $serviceSelect);
	$smarty->assign('yearSelect', $yearSelect);
	$smarty->assign('monthSelect', $monthSelect);
	$smarty->assign('beginSort', $beginSort);
	$smarty->assign('endSort', $endSort);
	$smarty->assign('typeSelect', $typeSelect);
	$smarty->assign('maxResultSelect', $maxResultSelect);

	if(isset($_GET['beginSort'])){
		$_GET['beginSort'] = intval($_GET['beginSort']);
	}else{
		$_GET['beginSort'] = 1;
	}

	if(isset($_GET['endSort'])){
		$_GET['endSort'] = intval($_GET['endSort']);
	}else{
		$_GET['endSort'] = 1;
	}

	if(isset($_GET['yearSelect'])){
		$_GET['yearSelect'] = intval($_GET['yearSelect']);
	}else{
		$_GET['yearSelect'] = 0;
	}

	if(isset($_GET['monthSelect'])){
		$_GET['monthSelect'] = intval($_GET['monthSelect']);
	}else{
		$_GET['monthSelect'] = 0;
	}

	if(isset($_GET['typeSelect'])){
		$_GET['typeSelect'] = intval($_GET['typeSelect']);
	}else{
		$_GET['typeSelect'] = 0;
	}

	if(isset($_GET['maxResultSelect'])){
		$_GET['maxResultSelect'] = intval($_GET['maxResultSelect']);
	}else{
		$_GET['maxResultSelect'] = 20;
	}

	if(isset($_GET['page'])){
		$_GET['page'] = intval($_GET['page']);
	}else{
		$_GET['page'] = 1;
	}

$events = array();

if(isset($_GET['serviceSelect'])){
	$params = array();
	$sql = "SELECT S.nameForUsers, strftime('%s', E.beginDate) AS beginDate, strftime('%s', E.endDate) AS endDate, ED.description, EI.isScheduled".
		" FROM events E, events_isou EI, events_description ED, services S".
		" WHERE S.idService = EI.idService".
		" AND ED.idEventDescription = EI.idEventDescription".
		" AND E.idEvent = EI.idEvent".
		" AND E.typeEvent = 0".
		" AND S.nameForUsers IS NOT NULL";

	if($_GET['typeSelect'] === 1){
		$filter = " AND EI.isScheduled = 1";
	}else if($_GET['typeSelect'] === 2){
		$filter = " AND EI.isScheduled = 0";
	}else{
		$filter = " AND EI.isScheduled < 2";
	}

	if(isset($serviceSelect[$_GET['serviceSelect']]) && $_GET['serviceSelect'] != 'all'){
		$filter .= " AND EI.idService = ?";
		$params[] = $_GET['serviceSelect'];
	}

	if($_GET['yearSelect'] !== 0){
		if($_GET['yearSelect'] == date('Y')){
			$db_stat = $db;
		}else{
			try{
				$db_name = substr(str_replace('isou.sqlite3', 'isou-'.$_GET['yearSelect'].'.sqlite3', DB_PATH), 7);
				if(!is_file($db_name)){
					throw new PDOException($db_name.' n\'existe pas.');
				}
				$db_stat = new PDO('sqlite:'.$db_name, '', '');
			}catch(PDOException $e){
				$db_stat = null;
			}
		}

		if($_GET['monthSelect'] !== 0){
			// 2012-01-10T04:20
			if($_GET['monthSelect'] < 10){
				$begin = $_GET['yearSelect'].'-0'.$_GET['monthSelect'].'-01T00:00';
				$end = $_GET['yearSelect'].'-0'.($_GET['monthSelect']+1).'-01T00:00';
			}else{
				$begin = $_GET['yearSelect'].'-'.$_GET['monthSelect'].'-01T00:00';
				$end = $_GET['yearSelect'].'-'.($_GET['monthSelect']+1).'-01T00:00';
			}
		}else{
			$begin = $_GET['yearSelect'].'-01-01T00:00';
			$end = $_GET['yearSelect'].'-12-31T23:59';
		}
		$filter .= " AND E.beginDate BETWEEN ? AND ?";
		$params[] = $begin;
		$params[] = $end;
	}

	if ($_SESSION['hide'] === 1){
		$filter .= " AND (E.endDate IS NULL OR (strftime('%s', E.endDate) - strftime('%s', E.beginDate) > ".$CFG['tolerance']."))";
	}

	($_GET['beginSort'] === 1)?$_GET['beginSort'] = ' DESC':$_GET['beginSort'] = '';
	($_GET['endSort'] === 1)?$_GET['endSort'] = ' DESC':$_GET['endSort'] = '';

	// LIMIT [nombre_ligne] OFFSET [debut]
	$sql .= $filter." ORDER BY E.beginDate".$_GET['beginSort'].", E.endDate".$_GET['endSort'];

	if($_GET['maxResultSelect'] !== -1){
		$sql .= " LIMIT ".(($_GET['maxResultSelect']+1)*10)." OFFSET ".(($_GET['page']-1)*(($_GET['maxResultSelect']+1)*10));
	}

	if($db_stat !== NULL){
		$query = $db_stat->prepare($sql);
		$query->execute($params);

		$total = 0;
		while($event = $query->fetch(PDO::FETCH_OBJ)){
			if($event->endDate === NULL){
				$event->endDate = TIME;
			}
			$event->total = round(($event->endDate-$event->beginDate)/60);
			$total += $event->total;
			$events[] = $event;
		}

		/* * * * * * * * * * * * * * * * * *
		 * GENERATION PAGE
		 * * * * * * * * * * * * * * * * * */
		$sql = "SELECT count(*)".
				" FROM events_isou EI, events E".
				" WHERE E.idEvent = EI.idEvent".
				$filter;

		$cnt = $db_stat->prepare($sql);
		$cnt->execute($params);
		$cnt = $cnt->fetch();
		if($_GET['maxResultSelect'] == -1){
			$nbPage = 1;
		}else{
			$nbPage = ceil($cnt[0]/(($_GET['maxResultSelect']+1)*10));
		}

		$fullUrl = get_base_url('full', HTTPS);
		if(strpos($fullUrl, 'page=') === FALSE){
			$fullUrl = get_base_url('full', HTTPS).'&amp;page='.$_GET['page'];
		}

		$range = 3;
		$pageRange = '';

		// 3 plages et 2 ...
		if($nbPage < $range*3+$range*2){
			for($i=1;$i<=$nbPage;$i++){
				$pageRange .= '<a href="'.str_replace('page='.$_GET['page'],'page='.$i,$fullUrl).'" title="Aller à la page '.$i.'">'.$i.'</a> ';
			}
		}else{
			if($_GET['page'] < $range*2 || $_GET['page']-1 > ($nbPage-$range*2)){
				// les pages du milieu de defaut ; page appelee est soit en debut, soit en fin
				$startMidRange = ceil($nbPage/2)-ceil($range/2);
				$endMidRange = ceil($nbPage/2)+ceil($range/2);
			}else{
				// les pages autour de la page demandée
				$startMidRange = $_GET['page']-ceil($range/2);
				$endMidRange =	$_GET['page']+ceil($range/2);
			}

			$middleRange = '';
			for($i=$startMidRange;$i < $endMidRange;$i++){
				$middleRange .= '<a href="'.str_replace('page='.$_GET['page'],'page='.$i,$fullUrl).'" title="Aller à la page '.$i.'">'.$i.'</a> ';
			}
			$middleRange = '... '.$middleRange.'... ';

			if($_GET['page'] >= $range && $_GET['page'] < $range*2){
				$irange = $_GET['page']+1;
			}else{
				$irange = $range;
			}

			for($i=1;$i<=$irange;$i++){
				$pageRange .= '<a href="'.str_replace('page='.$_GET['page'],'page='.$i,$fullUrl).'" title="Aller à la page '.$i.'">'.$i.'</a> ';
			}
			$pageRange .= $middleRange;


			if($_GET['page'] <= $nbPage-$range+1 && $_GET['page']-1 > $nbPage-$range*2){
				$irange = $_GET['page']-1;
			}else{
				$irange = $nbPage-$range+1;
			}

			for($i=$irange;$i<=$nbPage;$i++){
				$pageRange .= '<a href="'.str_replace('page='.$_GET['page'],'page='.$i,$fullUrl).'" title="Aller à la page '.$i.'">'.$i.'</a> ';
			}
		}

		$pageRange = str_replace('Aller à la page '.$_GET['page'].'"','Aller à la page '.$_GET['page'].'" class="selectedIndex"',$pageRange);
	}
}

$smarty->assign('events', $events);
if(isset($total)){
	$smarty->assign('total', $total);
}
if(isset($pageRange)){
	$smarty->assign('pageRange', $pageRange);
}
$db_stat = NULL;
if(isset($_GET['export'])){
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=isou_export.csv");
	header("Content-Type: text/csv; charset=utf-8");
	header("Content-Transfer-Encoding: binary");
	$smarty->display('statistics/history_export.tpl');
	exit(0);
}

?>
