<?php

/* calcul les statistiques de la semaine */

$today = getdate();
$limit = mktime(23,59,59)-(24*60*60*$today['wday']);
$sql = "SELECT os, browser, ip, userAgent, dateVisit".
		" FROM statistics".
		" WHERE dateVisit <= ".$limit;

// semaine lundi 00h - dimanche 23h59
$insertRecords = array();
$dbNameStat = '';

if($statisticRecords = $db->query($sql)){
	$toto = 0;
	while($statisticRecord = $statisticRecords->fetch(PDO::FETCH_NUM)){
		$countInsertRecords = count($insertRecords);

		$found = FALSE;
		$i = 0;

		while($i<$countInsertRecords && $found === FALSE){
			if($statisticRecord[4] >= $insertRecords[$i]->weeks && $statisticRecord[4] <= ($insertRecords[$i]->weeks+((7*24*60*60)-1))){
				if($statisticRecord[0] === $insertRecords[$i]->os &&
					$statisticRecord[1] === $insertRecords[$i]->browser &&
					$statisticRecord[2] === $insertRecords[$i]->ip &&
					$statisticRecord[3] === $insertRecords[$i]->userAgent){

					$d1 = getdate($statisticRecord[4]);
					$d2 = getdate($insertRecords[$i]->weeks);

					// switch entre chaque annee
					if($d1['year'] === $d2['year']){
						$insertRecords[$i]->numOf++;
						$found = TRUE;
					}

				}
			}
			$i++;
		}

		if($found === FALSE){
			$insertRecords[$countInsertRecords] = new stdClass();
			$weekDay = getdate($statisticRecord[4]);

			if($weekDay['wday'] === 0){
				$weekDay['wday'] = 7;
			}

			// timestamp du lundi 0:00:01
			$insertRecords[$countInsertRecords]->weeks = mktime(0,0,0,$weekDay['mon'],$weekDay['mday'],$weekDay['year'])-(24*60*60*($weekDay['wday']-1));

			$d1 = getdate($insertRecords[$countInsertRecords]->weeks);
			if($weekDay['year'] !== $d1['year']){
				$insertRecords[$countInsertRecords]->weeks = mktime(0,0,0,1,1,$weekDay['year']);
			}

			$insertRecords[$countInsertRecords]->os = $statisticRecord[0];
			$insertRecords[$countInsertRecords]->browser = $statisticRecord[1];
			$insertRecords[$countInsertRecords]->ip = $statisticRecord[2];
			$insertRecords[$countInsertRecords]->userAgent = $statisticRecord[3];
			$insertRecords[$countInsertRecords]->numOf = 1;
		}
	}
}


$countInsertRecords = count($insertRecords);

if($countInsertRecords > 0){
	try{
		$dbStat = new PDO(DB_STAT_PATH, '', '');
	}catch(PDOException $e){
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', $e->getMessage());
		exit(1);
	}
}

for($i=0;$i<$countInsertRecords;$i++){
	$data = $insertRecords[$i];
	$sql = "INSERT INTO visits VALUES(?,?,?,?,?,?)";
	$query = $dbStat->prepare($sql);
	if($query->execute(array($data->weeks, $data->numOf, $data->os, $data->browser, $data->ip, $data->userAgent)) === true){
		// success
		// echo '<p>insert into statistics values('.$data->weeks.','.$data->numOf.','.$data->os.','.$data->ip.','.$data->userAgent.')</p>';
	}else{
		echo 'La semaine '.$data->weeks.' n\'a pas été ajoutée';
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', 'La semaine '.$data->weeks.' n\'a pas été ajoutée');
		exit(1);
	}
}

$sql = "DELETE FROM statistics";
$query = $db->exec($sql);

$dbStat = null;
$db = null;

?>
