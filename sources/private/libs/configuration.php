<?php

function set_configuration($key, $value, $field=NULL){
	global $DB;

	$sql = "UPDATE configuration SET value=? WHERE key=?";
	$query = $DB->prepare($sql);
	if($query->execute(array($value, $key))){
		if($field === NULL){
			$_POST['successes'][] = 'Mise à jour de la clé "'.$key.'".';
		}else{
			$_POST['successes'][] = 'Mise à jour du champ "'.$field.'".';
		}

		return true;
	}else{
		if($field === NULL){
			$_POST['errors'][] = 'Erreur lors de la mise à jour de la clé "'.$key.'".';
		}else{
			$_POST['errors'][] = 'Erreur lors de la mise à jour du champ "'.$field.'".';
		}

		return false;
	}
}

?>
