<?php

$year = intval(strftime('%Y', TIME));

if(!is_file(BASE.'/database/isou-'.($year-1).'.sqlite3')){
	$newyear = mktime(0, 0, 0, 1, 1, $year);
	$oldyear = mktime(23, 59, 59, 12, 31, $year-1);

	// fermer tous les évènements en cours
	$sql = "UPDATE events SET endDate = ? WHERE endDate IS NULL";
	$query = $db->prepare($sql);
	$query->execute(array($oldyear));

	if(copy(BASE.'/database/isou.sqlite3', BASE.'/database/isou-'.($year-1).'.sqlite3')){
		$sql = "DELETE FROM events_isou WHERE idEvent = (SELECT idEvent FROM events WHERE endDate < ? AND endDate IS NOT NULL)";
		$query = $db->prepare($sql);
		$query->execute(array($newyear));

		$sql = "DELETE FROM events_nagios WHERE idEvent = (SELECT idEvent FROM events WHERE endDate < ? AND endDate IS NOT NULL)";
		$query = $db->prepare($sql);
		$query->execute(array($newyear));

		$sql = "DELETE FROM events WHERE endDate < ? AND endDate IS NOT NULL";
		$query = $db->prepare($sql);
		$query->execute(array($newyear));

		$sql = "UPDATE events SET beginDate = ? WHERE beginDate < ?";
		$query = $db->prepare($sql);
		$query->execute(array($newyear, $newyear));
	}else{
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', 'Les bases n\'ont pas pu être dupliquées.');
	}
}

?>
