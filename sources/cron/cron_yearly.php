<?php

$pwd = dirname(__FILE__).'/..';

require $pwd.'/functions.php';
require $pwd.'/config.php';
require BASE.'/php/common_database.php';

$year = getdate(TIME);
$year = $year['year'];

$sql = "UPDATE events SET endDate = ".(TIME-1)." WHERE endDate IS NULL";
$db->query($sql);

if(!is_file(BASE.'/database/isou'.$year['year'].'.sqlite3')){
	if(copy(BASE.'/database/isou.sqlite3', BASE.'/database/isou'.$year['year'].'.sqlite3')){
		$sql = "DELETE FROM events_isou WHERE idEvent = (SELECT idEvent FROM events WHERE endDate < ".TIME." AND endDate IS NOT NULL)";
		$query = $db->prepare($sql);
		$query->execute();

		$sql = "DELETE FROM events_nagios WHERE idEvent = (SELECT idEvent FROM events WHERE endDate < ".TIME." AND endDate IS NOT NULL)";
		$query = $db->prepare($sql);
		$query->execute();

		$sql = "DELETE FROM events WHERE endDate < ".TIME." AND endDate IS NOT NULL";
		$query = $db->prepare($sql);
		$query->execute();

		$sql = "UPDATE events SET beginDate = ".TIME." WHERE beginDate < ".TIME;
		$query = $db->prepare($sql);
		$query->execute();
	}else{
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', 'Les bases n\'ont pas pu être dupliquées.');
	}
}

?>
