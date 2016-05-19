<?php

use UniversiteRennes2\Isou;

require PRIVATE_PATH.'/classes/isou/state.php';

function get_state($id){
	global $DB;

	$sql = "SELECT id, name, title, alternative_text, image FROM states WHERE id=?";
	$query = $DB->prepare($sql);
	$query->execute(array($id));

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\State');

	return $query->fetch();
}

function get_states(){
	global $DB;

	$sql = "SELECT id, name, title, alternative_text, image FROM states ORDER BY id";
	$query = $DB->prepare($sql);
	$query->execute();
	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\State');
}

?>
