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

	if(isset($_GET['yearSort'])){
		$_GET['yearSort'] = intval($_GET['yearSort']);
	}else{
		$_GET['yearSort'] = 0;
	}

	if(isset($_GET['monthSort'])){
		$_GET['monthSort'] = intval($_GET['monthSort']);
	}else{
		$_GET['monthSort'] = 0;
	}

	if(isset($_GET['typeSelect'])){
		$_GET['typeSelect'] = intval($_GET['typeSelect']);
	}else{
		$_GET['typeSelect'] = 0;
	}

	if(isset($_GET['maxResult'])){
		$_GET['maxResult'] = intval($_GET['maxResult']);
	}else{
		$_GET['maxResult'] = 20;
	}

	if(isset($_GET['page'])){
		$_GET['page'] = intval($_GET['page']);
	}else{
		$_GET['page'] = 1;
	}

$events = array();

if(isset($_GET['serviceSelect'])){

	$sql = "SELECT S.nameForUsers, E.beginDate, E.endDate, ED.description, EI.isScheduled".
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
		$filter .= " AND EI.idService = ".intval($_GET['serviceSelect']);
	}

	if($_GET['yearSort'] !== 0){
		if($_GET['monthSort'] !== 0){
			$begin = mktime(0,0,0,$_GET['monthSort'],1,$_GET['yearSort']);
			$end = mktime(0,0,0,$_GET['monthSort']+1,1,$_GET['yearSort']);
		}else{
			$begin = mktime(0,0,0,1,1,$_GET['yearSort']);
			$end = mktime(23,59,59,12,31,$_GET['yearSort']);
		}
		$filter .= " AND E.beginDate BETWEEN ".$begin." AND ".$end;
	}

	($_GET['beginSort'] === 1)?$_GET['beginSort'] = ' DESC':$_GET['beginSort'] = '';
	($_GET['endSort'] === 1)?$_GET['endSort'] = ' DESC':$_GET['endSort'] = '';

	$sql .= $filter." ORDER BY E.beginDate".$_GET['beginSort'].", E.endDate".$_GET['endSort'].
		" LIMIT ".($_GET['page']-1)*$_GET['maxResult'].", ".$_GET['maxResult'];

	$query = $db->query($sql);
	$total = 0;
	while($event = $query->fetch(PDO::FETCH_OBJ)){
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

	$cnt = $db->query($sql);
	$cnt = $cnt->fetch();
	$nbPage = ceil($cnt[0]/$_GET['maxResult']);

	if(isset($_GET['page'])){
		$fullUrl = get_base_url('full', HTTPS);
	}else{
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

$smarty->assign('events', $events);
if(isset($total)){
	$smarty->assign('total', $total);
}
if(isset($pageRange)){
	$smarty->assign('pageRange', $pageRange);
}

?>

