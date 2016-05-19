<?php

require PRIVATE_PATH.'/classes/isou/announcement.php';

function get_announcement(){
	global $DB;

	$sql = "SELECT message, visible, author, last_modification".
		" FROM announcement";
	$query = $DB->prepare($sql);
	$query->execute();

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Announcement');

	return $query->fetch();
}

function get_visible_announcement(){
	global $DB;

	$sql = "SELECT message, visible, author, last_modification".
		" FROM announcement".
		" WHERE visible = 1".
		" AND message != ''";
	$query = $DB->prepare($sql);
	$query->execute();

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Announcement');

	return $query->fetch();
}

?>
