<?php

$pwd = dirname(__FILE__).'/..';

require $pwd.'/functions.php';
require $pwd.'/config.php';
require BASE.'/php/common_database.php';

$year = getdate(TIME);
$year = $year['year'];

$sql = "UPDATE events SET endDate = ".TIME." WHERE endDate IS NULL";
$db->query($sql);

$sql = "UPDATE events_nagios SET endDate = ".TIME." WHERE endDate IS NULL";
$db->query($sql);

if(!is_file(BASE.'/database/isou'.$year['year'].'.sqlite3')){
	if(copy(BASE.'/database/isou.sqlite3', BASE.'/database/isou'.$year['year'].'.sqlite3')){
		$sql = "DELETE FROM events";
		$db->query($sql);
		$sql = "DELETE FROM events_nagios";
		$db->query($sql);
	}else{
		add_log(LOG_FILE, 'ISOU', 'ERROR_DB', 'Les bases n\'ont pas pu être dupliquées.');
	}
}

?>
