<?php

namespace UniversiteRennes2\Isou;

class Dependency_Group_Content{
	public $idgroup;
	public $idservice;
	public $servicestate;

	public function __construct(){
		if(!isset($this->idgroup)){
			// instance manuelle
			$this->idgroup = 0;
			$this->idservice = 0;
			$this->servicestate = 1;
		}
	}

	public function check_data($groups, $services, $states){
		$errors = array();

		if(!isset($groups[$this->idgroup])){
			$errors[] = 'Le groupe choisi est invalide.';
		}

		$found = 0;
		foreach($services as $subservices){
			$found |= isset($subservices[$this->idservice]);
		}

		if($found === 0){
			$errors[] = 'Le service choisi est invalide.';
		}

		if(!isset($states[$this->servicestate])){
			$errors[] = 'L\'état choisi est invalide.';
		}

		return $errors;
	}


	public function save(){
		global $DB;

		$results = array('successes' => array(), 'errors' => array());
		$params = array($this->idgroup, $this->idservice, $this->servicestate);

		if(isset($this->old_idgroup, $this->old_service)){
			$sql = "UPDATE dependencies_groups_content SET idgroup=?, idservice=?, servicestate=? WHERE idgroup=? AND idservice=?";
			$params[] = $this->old_idgroup;
			$params[] = $this->old_idservice;
		}else{
			$sql = "INSERT INTO dependencies_groups_content(idgroup, idservice, servicestate) VALUES(?,?,?)";
		}
		$query = $DB->prepare($sql);

		if($query->execute($params)){
			$results['successes'] = array('Les données ont été correctement enregistrées.');
		}else{
			// log db errors
			$sql_error = $query->errorInfo();
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] ".implode(', ', $sql_error)."\n", FILE_APPEND);

			$results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
		}

		return $results;
	}

	public function change_state($state){
		global $DB;

		$results = array('successes' => array(), 'errors' => array());
		$params = array($state, $this->idgroup, $this->idservice);

		$sql = "UPDATE dependencies_groups_content SET servicestate=? WHERE idgroup=? AND idservice=?";
		$query = $DB->prepare($sql);

		if($query->execute($params)){
			$results['successes'] = array('Les données ont été correctement enregistrées.');
		}else{
			// log db errors
			$sql_error = $query->errorInfo();
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] ".implode(', ', $sql_error)."\n", FILE_APPEND);

			$results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
		}

		return $results;
	}

	public function delete(){
		global $DB;

		$results = array('successes' => array(), 'errors' => array());

		$sql = "DELETE FROM dependencies_groups_content WHERE idgroup=? AND idservice=?";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->idgroup, $this->idservice))){
			$results['successes'] = array('Les données ont été correctement supprimées.');
		}else{
			// log db errors
			$sql_error = $query->errorInfo();
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] ".implode(', ', $sql_error)."\n", FILE_APPEND);

			$results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
		}

		return $results;
	}
}

?>
