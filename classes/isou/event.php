<?php

namespace UniversiteRennes2\Isou;

class Event{
	const TYPE_UNSCHEDULED = '0';
	const TYPE_SCHEDULED = '1';
	const TYPE_REGULAR = '2';
	const TYPE_CLOSED = '3';

	public $id;
	public $begindate;
	public $enddate;
	public $state;
	public $type;
	public $period;
	public $ideventdescription;
	public $description;
	public $idservice;

	public static $TYPES = array(
		self::TYPE_SCHEDULED => 'Évènement prévu',
		self::TYPE_UNSCHEDULED => 'Évènement imprévu',
		self::TYPE_REGULAR => 'Évènement régulier',
		self::TYPE_CLOSED => 'Service fermé',
		);
	public static $PERIODS = array(
		'0' => 'Aucune',
		'86400' => 'Tous les jours',
		'604800' => 'Toutes les semaines',
		);

	public function __construct(){
		if(isset($this->id)){
			// PDO instance
			try{
				$this->begindate = new \DateTime($this->begindate);
				if($this->enddate !== NULL){
					$this->enddate = new \DateTime($this->enddate);
				}
			}catch(Exception $exception){
				$this->begindate = new \DateTime();
				$this->enddate = new \DateTime();
			}

			if(empty($this->period)){
				$this->period = '0';
			}

		}else{
			// manual instance
			$this->id = 0;
			$this->begindate = new \DateTime();
			$this->enddate = NULL;
			$this->state = State::CRITICAL;
			$this->type = self::TYPE_SCHEDULED;
			$this->period = '0';
			$this->ideventdescription = 1;
			$this->description = NULL;
			$this->idservice = 0;
		}
	}

	public function set_service($idservice, $options_services=NULL){
		global $DB;

		$this->idservice = $idservice;

		if($options_services === NULL){
			require_once PRIVATE_PATH.'/libs/services.php';

			$options_services = get_isou_services_sorted_by_idtype();
		}

		if(!isset($options_services[$this->idservice])){
			throw new \Exception('Le service mis en maintenance n\'est pas valide.');
		}else{
			$sql = "SELECT COUNT(E.id) AS total".
					" FROM events E".
				" WHERE E.id != ?".
				" AND E.idservice = ?".
				" AND (E.enddate IS NULL OR (E.enddate >= ? AND E.begindate <= ?))";
			$query = $DB->prepare($sql);
			$query->execute(array($this->id, $this->idservice, STR_TIME, STR_TIME));
			$count = $query->fetch(\PDO::FETCH_OBJ);
			if($count->total !== '0'){
				throw new \Exception('Un évènement est déjà en cours pour ce service. Veuillez modifier ou supprimer l\'ancien évènement.');
			}
		}
	}

	public function set_period($period){
		$this->period = $period;

		if(empty($this->period)){
			$this->period = NULL;
		}else{
			if(!isset(self::$PERIODS[$this->period])){
				throw new \Exception('La périodicité n\'est pas valide.');
			}

			if($this->type === self::TYPE_UNSCHEDULED){
				throw new \Exception('Un évènement imprévu ne peut pas avoir de périodicité.');
			}

			if($this->enddate === NULL){
				throw new \Exception('Veuillez indiquer une date de fin.');
			}

			$interval = $this->begindate->diff($this->enddate);
			if($interval->days > 0){
				throw new \Exception('L\'évènement doit durer moins de 24 heures.');
			}

		}
	}

	public function set_type($type){
		$this->type = $type;

		if(!isset(self::$TYPES[$this->type])){
			throw new \Exception('Le type d\'opération n\'est pas valide.');
		}
	}

	public function set_begindate($begindate){
		try{
			if(preg_match('#^\d{2}/\d{2}/\d{4} \d{2}:\d{2}$#', $begindate)){
				$begindate = substr($begindate, 6, 4).'-'.substr($begindate, 3, 2).'-'.substr($begindate, 0, 2).'T'.substr($begindate, 11, 2).':'.substr($begindate, 14, 2);
			}else{
				throw new \Exception();
			}

			$this->begindate = new \DateTime($begindate);
		}catch(Exception $exception){
			$this->begindate = new \DateTime();

			throw new \Exception('La date de début d\'interruption doit être au format JJ/MM/AAAA HH:MM.');
		}
	}

	public function set_enddate($enddate){
		if(empty($enddate)){
			$this->enddate = NULL;
		}else{
			try{
				if(preg_match('#^\d{2}/\d{2}/\d{4} \d{2}:\d{2}$#', $enddate)){
					$enddate = substr($enddate, 6, 4).'-'.substr($enddate, 3, 2).'-'.substr($enddate, 0, 2).'T'.substr($enddate, 11, 2).':'.substr($enddate, 14, 2);
				}else{
					throw new \Exception();
				}

				$this->enddate = new \DateTime($enddate);
			}catch(Exception $exception){
				$this->enddate = new \DateTime();

				throw new \Exception('La date de fin d\'interruption doit être au format JJ/MM/AAAA HH:MM.');
			}

			if(($this->begindate < $this->enddate) === FALSE){
				throw new \Exception('La date de début doit être inférieure à la date de fin.');
			}
		}
	}

	public function set_state($state, $options_states=NULL){
		$this->state = $state;

		if($options_states === NULL){
			$options_states = State::$STATES;
		}

		if(!isset($options_states[$this->state])){
			throw new \Exception('L\'état du service a une valeur incorrecte.');
		}
	}

	public function save(){
		global $DB, $LOGGER;

		if($this->enddate === NULL){
			$enddate = NULL;
		}else{
			$enddate = $this->enddate->format('Y-m-d\TH:i');
		}

		$params = array($this->begindate->format('Y-m-d\TH:i'), $enddate, $this->state, $this->type, $this->period, $this->ideventdescription, $this->idservice);

		if($this->id === 0){
			$sql = "INSERT INTO events(begindate, enddate, state, type, period, ideventdescription, idservice) VALUES(?,?,?,?,?,?,?)";
		}else{
			$sql = "UPDATE events SET begindate=?, enddate=?, state=?, type=?, period=?, ideventdescription=?, idservice=? WHERE id=?";
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

			throw new \Exception('Une erreur est survenue lors de l\'enregistrement de l\'évènement.');
		}
	}

	public function delete(){
		global $DB, $LOGGER;

		$sql = "DELETE FROM events WHERE idevent=?";
		$query = $DB->prepare($sql);

		if($query->execute(array($this->id)) === FALSE){
			// log db errors
			$LOGGER->addError(implode(', ', $query->errorInfo()));

			throw new \Exception('Une erreur est survenue lors de la suppression de l\'évènement.');
		}
	}

	public function close(){
		global $DB, $LOGGER;

		$sql = "UPDATE events SET enddate=? WHERE idevent=?";
		$query = $DB->prepare($sql);
		if($query->execute(array(STR_TIME, $this->id))){
			$this->enddate = new \DateTime(STR_TIME);
			return TRUE;
		}else{
			$LOGGER->addError(implode(', ', $query->errorInfo()));
			return FALSE;
		}
	}

	public function close_all_other_events(){
		global $DB, $LOGGER;

		$sql = "UPDATE events SET enddate=? WHERE idevent!=? AND idservice=? AND begindate <= ? AND (enddate IS NULL OR enddate >= ?)";
		$query = $DB->prepare($sql);
		if($query->execute(array(STR_TIME, $this->id, $this->idservice, STR_TIME, STR_TIME))){
			return TRUE;
		}else{
			$LOGGER->addError(implode(', ', $query->errorInfo()));
			return FALSE;
		}
	}

	public function set_description($description=NULL, $autogen=0){
		global $DB, $LOGGER;

		if($description !== NULL){
			$this->description = $description;
		}
		$description = get_event_description_by_content($this->description);

		if($description === FALSE){
			$sql = "INSERT INTO events_descriptions(description, autogen) VALUES(?,?)";
			$query = $DB->prepare($sql);
			if($query->execute(array($this->description, $autogen))){
				$this->ideventdescription = $DB->lastInsertId();
				$this->description = $description;
			}else{
				// log db errors
				$LOGGER->addError(implode(', ', $query->errorInfo()));
				return FALSE;
			}
		}else{
			$this->ideventdescription = $description->ideventdescription;
			$this->description = $description;
		}

		return TRUE;
	}

	public function __toString(){
		$str = '';

		if($this->state === State::CLOSED){
			$str = 'Service fermé depuis le '.strftime('%A %d %B %Y', $this->begindate->getTimestamp()).'.';
			if($this->enddate !== NULL){
				$str .= ' Réouverture le '.strftime('%A %d %B %Y', $this->enddate->getTimestamp()).'.';
			}
		}elseif(!empty($this->period)){
			if($this->period === 86400){
				$period = ' quotidienne';
			}elseif($this->period === 604800){
				$period = ' hebdomadaire';
			}else{
				$period = '';
			}

			if(TIME > $this->begindate->getTimestamp()){
				$str = 'Le service est en maintenance'.$period.' de '.strftime('%Hh%M', $this->begindate->getTimestamp()).' à '.strftime('%Hh%M', $this->enddate->getTimestamp()).'.';
			}else{
				$str = 'Le service sera en maintenance'.$period.' de '.strftime('%Hh%M', $this->begindate->getTimestamp()).' à '.strftime('%Hh%M', $this->enddate->getTimestamp()).'.';
			}
		}else{
			if($this->begindate->getTimestamp() > TIME){
				// évènement futur
				if($this->enddate === NULL){
					$str = 'Le service sera perturbé le '.strftime('%A %d %B à partir de %Hh%M', $this->begindate->getTimestamp()).'.';
				}else{
					$str = 'Le service sera perturbé du '.strftime('%A %d %B %Hh%M', $this->begindate->getTimestamp()).' au '.strftime('%A %d %B %Hh%M', $this->enddate->getTimestamp()).'.';
				}
			}elseif($this->enddate !== NULL && $this->enddate->getTimestamp() < TIME){
				// évènement passé
				if(strftime('%A%d%B', $this->begindate->getTimestamp()) === strftime('%A%d%B', $this->enddate->getTimestamp())){
					// évènement qui s'est déroulé sur une journée
					$str = 'Le service a été perturbé le '.strftime('%A %d %B', $this->begindate->getTimestamp()).' de '.strftime('%Hh%M', $this->begindate->getTimestamp()).' à '.strftime('%Hh%M', $this->enddate->getTimestamp()).'.';
				}else{
					// évènement qui s'est déroulé sur plusieurs journées
					$str = 'Le service a été perturbé du '.strftime('%A %d %B %Hh%M', $this->begindate->getTimestamp()).' au '.strftime('%A %d %B %Hh%M', $this->enddate->getTimestamp()).'.';
				}
			}else{
				// évènement en cours
				if(strftime('%A%d%B', $this->begindate->getTimestamp()) === strftime('%A%d%B')){
					$str = 'Le service est actuellement perturbé depuis '.strftime('%Hh%M', $this->begindate->getTimestamp()).'.';
				}else{
					$str = 'Le service est actuellement perturbé depuis le '.strftime('%A %d %B %Hh%M', $this->begindate->getTimestamp()).'.';
				}
			}
		}

		return $str;
	}

	/**
	*	@desc	Destruct instance
	*/
	public function __destruct() {
		// object destructed
	}
}

?>
