<?php

namespace UniversiteRennes2\Isou;

class Announcement{
	public $message;
	public $visible;
	public $autor;
	public $last_modification;

	public function __construct(){
		try{
			$this->last_modification = new \DateTime($this->last_modification);
		}catch(Exception $exception){
			$this->last_modification = new \DateTime();
		}
	}

	public function check_data($options_visible){
		$errors = array();

		$HTMLPurifier = new \HTMLPurifier();
		$this->message = $HTMLPurifier->purify($this->message);

		if(!isset($options_visible[$this->visible])){
			$errors[] = 'La valeur du champ "afficher l\'annonce" n\'est pas valide.';
		}elseif(empty($this->message)){
			$this->visible = '0';
		}

		return $errors;
	}

	public function save(){
		global $DB, $LOGGER;

		$results = array('successes' => array(), 'errors' => array());

		$last_modification = $this->last_modification->format(\DateTime::ATOM);

		$sql = "UPDATE announcement SET message =?, visible=?, autor=?, last_modification=?";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->message, $this->visible, $this->autor, $last_modification))){
			if($this->visible === '1'){
				$results['successes'][] = 'L\'annonce a bien été enregistrée.';
			}else{
				$results['successes'][] = 'L\'annonce a bien été retirée.';
			}

			$LOGGER->addInfo('Modification de l\'annonce', array('author' => $_SESSION['phpCAS']['user']));
		}else{
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			$results['errors'][] = 'La modification n\'a pas été enregistrée !';
		}

		return $results;
	}
}
