<?php

namespace UniversiteRennes2\Isou;

class Dependency_Group{
	public $id;
	public $name;
	public $redundant;
	public $groupstate;
	public $idservice;
	public $service;
	public $idmessage;
	public $message;

	public function __construct(){
		if(!isset($this->id)){
			// instance manuelle
			$this->id = 0;
			$this->name = 'Groupe non redondé';
			$this->redundant = 0;
			$this->groupstate = State::WARNING;
			$this->idservice = 1;
			$this->service = '';
			$this->idmessage = 1;
			$this->message = NULL;
		}
	}

	public function check_data($redundants, $states, $services){
		$errors = array();

		$this->name = htmlentities($this->name, ENT_NOQUOTES, 'UTF-8');
		if($this->name === ''){
			$errors[] = 'Le nom du groupe ne peut pas être vide.';
		}

		if(!isset($redundants[$this->redundant])){
			$errors[] = 'La valeur "redondée" choisie est invalide.';
		}

		if(!isset($states[$this->groupstate])){
			$errors[] = 'L\'état choisi est invalide.';
		}

		if(!isset($services[$this->idservice])){
			$errors[] = 'Le service choisi est invalide.';
		}

		$this->message = trim($this->message);
		$this->idmessage = $this->get_message();
		if($this->idmessage === FALSE){
			$this->idmessage = $this->set_message();
			if($this->idmessage === FALSE){
				$errors[] = 'Le message n\'a pas pu être inséré en base de données.';
			}
		}

		return $errors;
	}

	public function get_message(){
		global $DB;

		$sql = "SELECT id FROM dependencies_messages WHERE message = ?";
		$query = $DB->prepare($sql);
		$query->execute(array($this->message));
		if($message = $query->fetch(\PDO::FETCH_OBJ)){
			return $message->idmessage;
		}else{
			return FALSE;
		}
	}

	public function set_message(){
		global $DB, $LOGGER;

		$sql = "INSERT INTO dependencies_messages(message) VALUES(?)";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->message))){
			return $DB->lastInsertId();
		}else{
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			return FALSE;
		}
	}

	public function save(){
		global $DB, $LOGGER;

		$results = array('successes' => array(), 'errors' => array());
		$params = array($this->name, $this->redundant, $this->groupstate, $this->idservice, $this->idmessage);

		if($this->id === 0){
			$sql = "INSERT INTO dependencies_groups(name, redundant, groupstate, idservice, idmessage) VALUES(?,?,?,?,?)";
		}else{
			$sql = "UPDATE dependencies_groups SET name=?, redundant=?, groupstate=?, idservice=?, idmessage=? WHERE id=?";
			$params[] = $this->id;
		}
		$query = $DB->prepare($sql);

		if($query->execute($params)){
			if($this->id === 0){
				$this->id = $DB->lastInsertId();
			}
			$results['successes'] = array('Les données ont été correctement enregistrées.');
		}else{
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			$results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
		}

		return $results;
	}

	public function duplicate(){
		global $DB;

		$contents = get_dependency_group_contents($this->id);

		// create new group
		$this->id = 0;

		if($this->groupstate === State::WARNING){
			$this->groupstate = State::CRITICAL;
		}else{
			$this->groupstate = State::WARNING;
		}

		$results = $this->save();

		// toggle group contents
		foreach($contents as $content){
			$content->idgroup = $this->id;
			if($this->groupstate === State::CRITICAL){
				$content->servicestate = State::CRITICAL;
			}else{
				$content->servicestate = State::WARNING;
			}

			$content->save();
		}

		return $results;
	}

	public function delete(){
		global $DB, $LOGGER;

		$results = array('successes' => array(), 'errors' => array());
		$commit = 1;

		$DB->beginTransaction();

		$queries = array();
		$queries[] = "DELETE FROM dependencies_groups WHERE idgroup = ?";
		$queries[] = "DELETE FROM dependencies_groups_content WHERE idgroup = ?";

		foreach($queries as $sql){
			$query = $DB->prepare($sql);
			$commit &= $query->execute(array($this->id));
		}

		if($commit === 1){
			$DB->commit();
			$results['successes'] = array('Les données ont été correctement supprimées.');
		}else{
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			$DB->rollBack();
			$results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
		}

		return $results;
	}

	public function get_services(){
		global $DB;

		$sql = "SELECT s.id, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idplugin, s.idcategory".
			" FROM services s".
			" JOIN dependencies_groups dg ON s.id = dg.idservice".
			" WHERE dg.id = ?";
		$query = $DB->prepare($sql);
		$query->execute(array($this->id));

		$this->services = $query->fetchAll(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
	}

	public function get_content_services_sorted_by_id(){
		global $DB;

		$sql = "SELECT s.id, s.name".
			" FROM services s".
			" JOIN dependencies_groups_content dgc ON s.id = dgc.idservice".
			" WHERE dgc.idgroup = ?";
		$query = $DB->prepare($sql);
		$query->execute(array($this->id));

		$this->services = $query->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
	}

	public function get_content_services(){
		global $DB;

		$sql = "SELECT s.id, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idplugin, s.idcategory".
			" FROM services s".
			" JOIN dependencies_groups_content dgc ON s.id = dgc.idservice".
			" WHERE dgc.idgroup = ?";
		$query = $DB->prepare($sql);
		$query->execute(array($this->id));

		$this->services = $query->fetchAll(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
	}

	public function is_up(){
		global $DB;

		$this->get_content_services();

		foreach($this->services as $service){
			$sql = "SELECT idgroup, idservice, servicestate".
				" FROM dependencies_groups_content dgc".
				" WHERE idgroup = :idgroup".
				" AND idservice = :idservice".
				" AND servicestate <= :servicestate";

			$query = $DB->prepare($sql);
			$query->execute(array(':idgroup' => $this->id, ':idservice' => $service->id, ':servicestate' => $service->state));
			$status = $query->fetch(\PDO::FETCH_OBJ);

			if($status !== false && $this->redundant === '0'){
				// there is at least one service down :(
				return FALSE;
			}elseif($status === false && $this->redundant === '1'){
				// there is at least one service up !
				return TRUE;
			}
		}

		if($this->redundant === '0'){
			// there is no service down !
			return TRUE;
		}else{
			// there is no service up :(
			return FALSE;
		}
	}
}
