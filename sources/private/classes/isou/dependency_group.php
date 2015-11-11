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

	function __construct(){
		if(isset($this->idgroup)){
			// instance PDO
			$this->id = $this->idgroup;
			unset($this->idgroup);
		}else{
			// instance manuelle
			$this->id = 0;
			$this->name = 'Groupe non redondé';
			$this->redundant = 0;
			$this->groupstate = UniversiteRennes2\Isou\State::WARNING;
			$this->idservice = 1;
			$this->service = '';
			$this->idmessage = 1;
			$this->message = NULL;
		}
	}
}

?>
