<?php

	// Stats Visits
	$sql = "SELECT ip, count(*) AS total".
		" FROM statistics".
		" WHERE userAgent IS NULL".
		" GROUP BY ip".
		" ORDER BY ip";
	$visits = new stdClass();
	$visits->externe = 0;
	$visits->interne = 0;
	$visits->cri = 0;
	if($stats = $db->query($sql)){
		$total = 0;
		while($stat = $stats->fetch()){
			switch($stat[0]){
				case 0: $visits->externe = $stat[1];break;
				case 1: $visits->interne = $stat[1];break;
				default: $visits->cri = $stat[1];break;
			}
			$total += $stat[1];
		}
		$visits->count = $total;
	}

	// Stats Browsers
	$sql = "SELECT browser, count(*) AS total".
		" FROM statistics".
		" WHERE browser NOT LIKE 'other'".
		" GROUP BY browser".
		" ORDER BY total DESC";
	$browsers = array();
	if($stats = $db->query($sql)){
		while($stat = $stats->fetch(PDO::FETCH_OBJ)){
			$browsers[] = $stat;
		}
	}

	// Stats Operating System
	$sql = "SELECT os, count(*) AS total".
		" FROM statistics".
		" WHERE os NOT LIKE 'other'".
		" GROUP BY os".
		" ORDER BY total DESC";
	$os = array();
	if($stats = $db->query($sql)){
		while($stat = $stats->fetch(PDO::FETCH_OBJ)){
			$os[] = $stat;
		}
	}

	// Stats Bots
	$sql = "SELECT userAgent, count(*) AS total".
		" FROM statistics".
		" WHERE browser LIKE 'other'".
		" GROUP BY userAgent".
		" ORDER BY total DESC";
	$bots = array();
	$googlebot = new stdClass();
	$googlebot->userAgent = 'Googlebot';
	$googlebot->total = 0;
	$total = 0;
	if($stats = $db->query($sql)){
		while($stat = $stats->fetch(PDO::FETCH_OBJ)){
			if(strstr($stat->userAgent, 'Googlebot') !== false){
				$googlebot = $stat;
			}else{
				if(!empty($stat->userAgent)){
					$bots[] = $stat;
				}
				$total += $stat->total;
			}
		}
	}


$weeks = array();
$months = array();
$traffic = array();
$browsersTraffic = array();
$osTraffic = array();

if(is_file(substr(DB_STAT_PATH, 7))){
	try{
		$dbVisits = new PDO(DB_STAT_PATH);
	}catch(PDOException $e){
		echo "La création de la base de données a échoué (".$e->getMessage().")\n";
		continue;
	}

	$sql = "SELECT MIN(weeks) AS week FROM visits";
	$query = $dbVisits->prepare($sql);
	$query->execute();
	$firstYear = $query->fetchObject();
	$firstYear = strftime('%Y', $firstYear->week);
	if(isset($_GET['year'])){
		$_GET['year'] = intval($_GET['year']);
		if($firstYear <= $_GET['year'] && $_GET['year'] <= strftime('%Y')){
			$beginDate = mktime(0,0,0,1,1,$_GET['year']);
			$endDate = mktime(23,59,59,12,31,$_GET['year']);
		}
	}

	if(!isset($beginDate)){
		$beginDate = mktime(0,0,0,1,1,$firstYear);
		$endDate = TIME;
	}

	$years = range($firstYear, strftime('%Y'));

	// traffic hebdomadaire par IP
	$sql = "SELECT DISTINCT weeks".
			" FROM visits".
			" WHERE weeks BETWEEN ".$beginDate." AND ".$endDate.
			" ORDER BY weeks";
	$typeVisit = array('Visites externes', 'Visites internes', 'Visites CRI');

	if($query = $dbVisits->query($sql)){
		while($week = $query->fetch(PDO::FETCH_OBJ)){
			for($i=0;$i<3;$i++){
				$traffic[$typeVisit[$i]][$week->weeks] = 0;
			}
			$weeks[] = $week->weeks;
			if(!in_array(strftime('%Y %m', $week->weeks), $months)){
				$months[] = strftime('%Y %m', $week->weeks);
			}
		}
	}
	$countMonths = count($months);

	$sql = "SELECT weeks, ip, SUM(numOf) as count".
			" FROM visits".
			" WHERE userAgent IS NULL".
			" AND weeks BETWEEN ".$beginDate." AND ".$endDate.
			" GROUP BY weeks, ip".
			" ORDER BY weeks";
	if($query = $dbVisits->query($sql)){
		while($visit = $query->fetch(PDO::FETCH_OBJ)){
			$traffic[$typeVisit[$visit->ip]][$visit->weeks] = $visit->count;
		}
	}


	// visites mensuelles par navigateur
	$sql = "SELECT DISTINCT browser".
			" FROM visits".
			" WHERE browser != 'other'".
			" AND weeks BETWEEN ".$beginDate." AND ".$endDate.
			" ORDER BY browser";
	if($query = $dbVisits->query($sql)){
		while($browserVisits = $query->fetch(PDO::FETCH_OBJ)){
			for($i=0;$i<$countMonths;$i++){
				$browsersTraffic[$browserVisits->browser][$months[$i]] = 0;
			}
		}
	}

	$sql = "SELECT strftime('%Y %m', weeks, 'unixepoch') as month, browser, SUM(numOf) as count".
			" FROM visits".
			" WHERE browser != 'other'".
			" AND weeks BETWEEN ".$beginDate." AND ".$endDate.
			" GROUP BY month, browser".
			" ORDER BY month";
	if($query = $dbVisits->query($sql)){
		while($visit = $query->fetch(PDO::FETCH_OBJ)){
			$browsersTraffic[$visit->browser][$visit->month] = $visit->count;
		}
	}

	// visites mensuelles par systeme d'exploitation
	$sql = "SELECT DISTINCT os".
			" FROM visits".
			" WHERE os != 'other'".
			" AND weeks BETWEEN ".$beginDate." AND ".$endDate.
			" ORDER BY os";
	if($query = $dbVisits->query($sql)){
		while($osVisits = $query->fetch(PDO::FETCH_OBJ)){
			for($i=0;$i<$countMonths;$i++){
				$osTraffic[$osVisits->os][$months[$i]] = 0;
			}
		}
	}

	$sql = "SELECT strftime('%Y %m', weeks, 'unixepoch') as month, os, SUM(numOf) as count".
			" FROM visits".
			" WHERE weeks BETWEEN ".$beginDate." AND ".$endDate.
			" GROUP BY month, os".
			" ORDER BY month";
	if($query = $dbVisits->query($sql)){
		while($visit = $query->fetch(PDO::FETCH_OBJ)){
			$osTraffic[$visit->os][$visit->month] = $visit->count;
		}
	}
	$dbVisits = null;
}

	$visits->bots = $total+$googlebot->total;
	$smarty->assign('visits', $visits);
	$smarty->assign('browsers', $browsers);
	$smarty->assign('os', $os);
	$smarty->assign('bots', $bots);
	$smarty->assign('googlebot', $googlebot);
	$smarty->assign('weeks', $weeks);
	$smarty->assign('months', $months);
	$smarty->assign('years', $years);
	$smarty->assign('traffic', $traffic);
	$smarty->assign('browsersTraffic', $browsersTraffic);
	$smarty->assign('osTraffic', $osTraffic);
?>


