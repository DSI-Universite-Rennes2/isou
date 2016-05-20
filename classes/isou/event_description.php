<?php

namespace UniversiteRennes2\Isou;

class EventDescription{
	public $id;
	public $description;
	public $autogen;

	public function __construct(){
		if(!isset($this->id)){
			$this->id = 0;
			$this->description = '';
			$this->autogen = 0;
		}
	}

	public function save(){
		global $DB;

		$params = array($this->description, $this->autogen);

		if($this->id === 0){
			$sql = "INSERT INTO events_descriptions(description, autogen) VALUES(?,?)";
		}else{
			$sql = "UPDATE events_descriptions SET description=?, autogen=? WHERE ideventdescription=?";
			$params[] = $this->id;
		}

		$query = $DB->prepare($sql);

		if($query->execute($params)){
			if($this->id === 0){
				$this->id = $DB->lastInsertId();
			}
		}else{
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			throw new \Exception('Une erreur est survenue lors de l\'enregistrement de la description de l\'évènement.');
		}
	}

	public function delete(){
		global $DB;

		$sql = "DELETE FROM events_descriptions WHERE idevent=?";
		$query = $DB->prepare($sql);

		if($query->execute(array($this->id)) === FALSE){
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			throw new \Exception('Une erreur est survenue lors de la suppression de la description d\'évènement.');
		}
	}
}

?>
