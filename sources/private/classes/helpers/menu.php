<?php

namespace Isou\Helpers;

class Menu{
	public $id;
	public $label;
	public $title;
	public $url;
	public $model;
	public $position;
	public $selected;

	public function __construct(){
		if(isset($this->idmenu)){
			// instance PDO
			$this->id = $this->idmenu;
		}else{
			// instance manuelle
			$this->id = 0;
			$this->label = '';
			$this->title = '';
			$this->url = '';
			$this->model = '';
			$this->position = '';
		}

		$this->selected = FALSE;
	}

	public function save(){
		global $DB;

		$results = array('successes' => array(), 'errors' => array());

		$sql = "UPDATE menu SET active=? WHERE idmenu=?";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->active, $this->id))){
			if($this->active === '1'){
				$results['successes'][] = 'Le menu "'.$this->label.'" a été activé.';
			}else{
				$results['successes'][] = 'Le menu "'.$this->label.'" a été désactivé.';
			}
		}else{
			if($this->active === '1'){
				$this->active = '0';
			}else{
				$this->active = '1';
			}
			$results['errors'][] = 'Le menu "'.$this->label.'" n\'a pas été enregistré.';
		}

		return $results;
	}
}


