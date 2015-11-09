<?php

$year = intval(strftime('%Y', TIME));

if(!is_file(PRIVATE_PATH.'/database/isou-'.($year-1).'.sqlite3')){
	$newyear = $year.'-01-01T00:00';
	$oldyear = ($year-1).'-12-31T23:59';

	// fermer tous les évènements en cours
	$sql = "UPDATE events SET endDate = ? WHERE endDate IS NULL";
	$query = $DB->prepare($sql);
	$query->execute(array($oldyear));

	if(copy(PRIVATE_PATH.'/database/isou.sqlite3', PRIVATE_PATH.'/database/isou-'.($year-1).'.sqlite3')){
		$sql = "DELETE FROM events_isou WHERE idEvent = (SELECT idEvent FROM events WHERE endDate < ? AND endDate IS NOT NULL)";
		$query = $DB->prepare($sql);
		$query->execute(array($newyear));

		$sql = "DELETE FROM events_nagios WHERE idEvent = (SELECT idEvent FROM events WHERE endDate < ? AND endDate IS NOT NULL)";
		$query = $DB->prepare($sql);
		$query->execute(array($newyear));

		$sql = "DELETE FROM events WHERE endDate < ? AND endDate IS NOT NULL";
		$query = $DB->prepare($sql);
		$query->execute(array($newyear));

		$sql = "UPDATE events SET beginDate = ? WHERE beginDate < ?";
		$query = $DB->prepare($sql);
		$query->execute(array($newyear, $newyear));
	}else{
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', 'Les bases n\'ont pas pu être dupliquées.');
	}
}

?>
