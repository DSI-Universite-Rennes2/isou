<?php

/**
*	@desc		isou service class
*	@author 	CRI Rennes 2
*	@var		int	$id
*	@var		string	$name
*	@var		string	$nameForUsers
*	@var		int	$state
*	@var		string	$comment
*	@var		string	$categoryName
*	@var		IsouEvent array $events
*	@copyright	http://www.gnu.org/licenses/gpl-2.0.html
*	@version 	2.0
*	@since		1.0
*/

class IsouService {

	protected $id;
	protected $name;
	protected $nameForUsers;
	protected $state;
	protected $comment;
	protected $categoryName;
	protected $events;

	/**
	*	@desc		Constructor with params
	*	@param		int 	$id unique ID
	*	@param		string 	$name
	*	@param		string 	$nameForUsers
	*	@param		string 	$url
	*	@param		int 	$state				// current state : 0 for OK, 1 for orange, 2 for stopped, 3 for unknown state, 4 for closed
	*	@param		string 	$comment
	*	@param		string 	$categoryName
	*	@return		void
	*/
	public function __construct(){ // $id, $name = NULL, $nameForUsers = NULL, $url = NULL, $state = 3, $comment = NULL, $categoryName = NULL)
		$countArgs = func_num_args();
		$args = func_get_args();
		if(($countArgs === 0 && isset($this->idService)) || ($countArgs === 1 && is_object($args[0]))){
			// instanciation depuis un appel implicite de pdo (fetchObject)
			// instanciation depuis un appel explicite __construct(param) (stdClass ou pdo)
			$this->id = intval($this->idService);
			$this->name = $this->name;
			$this->nameForUsers = $this->nameForUsers;
			$this->url = $this->url;
			$this->state = intval($this->state);
			$this->comment = $this->comment;
			$this->categoryName = $this->category;
		}else if($countArgs === 7){
			// instanciation "manuelle" depuis un appel explicite __construct(param1, param2, etc...)
			$this->id = intval($args[0]);
			$this->name = $args[1];
			$this->nameForUsers = $args[2];
			$this->url = $args[3];
			$this->state = intval($args[4]);
			$this->comment = $args[5];
			$this->categoryName = $args[6];
		}else{
			$this->__destruct();
			new Exception('Pas assez de paramètres');
			$debug = debug_backtrace();
			die('Class IsouService (__construct) : pas assez de paramètres, fichier '.$debug[0]['file'].' ligne '.$debug[0]['line']);
		}

		if(empty($this->url)){
			$this->url = NULL;
		}

		if(empty($this->nameForUsers)){
			$this->name = $this->nameForUsers;
		}

		$this->events = array();
	}

	// Accessors GET
	public function getId()					{ return $this->id; }
	public function getName()				{ return $this->name; }
	public function getNameForUsers()		{ return $this->nameForUsers; }
	public function getUrl()				{ return $this->url; }
	public function getState()				{ return $this->state; }
	public function getComment()			{ return $this->comment; }
	public function getCategoryName()		{ return $this->categoryName; }

	// Accessors SET
	public function setState($state)		{ $this->state = $state; }
	public function setEvent($event)		{ $this->events[count($this->events)] = $event; }
	public function setEvents($events)		{ $this->events += $events; }

	/**
	*   @desc		Return next scheduled events if $now=true
	* 	@param		boolean $now
	*   @return		mixed IsouEvent array ; or NULL if no IsouEvent in array
	*/
	public function getEvents($now = false){
		if($now){
			$nextEvents = array();
			$now = TIME;
			for($i=0;$i<count($this->events);$i++){
				if($this->events[$i]->getEndDate() > $now){
					$nextEvents[count($nextEvents)] = $this->events[$i];
				}
			}
			if(count($nextEvents)>0){
				return $nextEvents;
			}else{
				return NULL;
			}
		}else{
			return $this->events;
		}
	}

	/**
	*   @desc		Return true if there is an event
	*   @return		boolean
	*/
	public function hasEvents(){
		return is_array($this->events) && count($this->events) > 0;
	}

	/**
	*   @desc		Return true if service is closed
	*   @return		boolean
	*/
	public function isClosed(){
		return $this->state === 4;
	}

	/**
	*   @desc		Remove an event from array of events
	* 	@param		int $id
	*   @return		nothing
	*/
	public function removeEvent($id) {
		$i = $id;
		while(isset($this->events[$i+1])){
			$this->events[$i] = $this->events[$i+1];
			$i++;
		}
		unset($this->events[$i]);
	}


	/**
	 * 	@desc 	return all next events in array, except closed and regular events
	 * 	@return array of IsouEvent
	 */
	public function getNextEvents($limit = -1){
		global $db;

		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description, EI.isScheduled".
		" FROM events E, events_isou EI, events_description D".
		" WHERE EI.idEvent = E.idEvent".
		" AND EI.idEventDescription = D.idEventDescription".
		" AND E.typeEvent = 0".
		" AND EI.idService = :0".
		" AND E.beginDate > :1".
		" AND EI.isScheduled < 2".
		" ORDER BY E.beginDate".
		" LIMIT :2";
		$event_records = $db->prepare($sql);
		$events = array();
		if($event_records->execute(array($this->id, TIME, $limit))){
			while($event = $event_records->fetch(PDO::FETCH_OBJ)){
				$event->serviceName = $this->nameForUsers;
				$event->state = $this->state;
				$event->isScheduled = 1; // ou 3 ? oO
				$event->type = 'NextInterruption';
				$events[] = new IsouEvent($event);
			}
		}
		return $events;
	}

	/**
	 * 	@desc 	return all scheduled events in array
	 * 	@return array of IsouEvent
	 */
	public function getScheduledEvents($tolerance = 0, $limit = -1, $beginDate = '', $endDate = ''){
		global $db;

		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description, EI.isScheduled".
				" FROM events E, events_isou EI, events_description D".
				" WHERE EI.idEvent = E.idEvent".
				" AND EI.idEventDescription = D.idEventDescription".
				" AND E.typeEvent = 0".
				" AND EI.idService = ?".
				" AND EI.isScheduled = 1".
				" AND ((E.endDate >= ?".
				" AND E.beginDate <= ?".
				" AND ((E.endDate-E.beginDate) > ?*1))".
				" OR (E.endDate IS NULL))".
				" ORDER BY E.beginDate".
				" LIMIT ?";

		$event_records = $db->prepare($sql);
		$events = array();
		if($event_records->execute(array($this->id, $beginDate, $endDate, $tolerance, $limit))){
			while($event = $event_records->fetch(PDO::FETCH_OBJ)){
				$event->serviceName = $this->nameForUsers;
				$event->state = $this->state;
				$event->isScheduled = 1; // nvm ?
				$event->type = NULL;
				$events[] = new IsouEvent($event);
			}
		}

		return $events;
	}

	/**
	 * 	@desc 	return all events in array, except closed and regular events
	 * 	@desc 	regular events are displayed only if there are active
	 * 	@return array of IsouEvent
	 */
	public function getAllEvents($tolerance = 0, $limit = -1, $beginDate = '', $endDate = ''){
		global $db;

		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description, EI.isScheduled".
				" FROM events E, events_isou EI, events_description D".
				" WHERE EI.idService = ?".
				" AND EI.idEventDescription = D.idEventDescription".
				" AND EI.idEvent = E.idEvent".
				" AND E.typeEvent = 0".
				" AND (".
				" (".
				// n'afficher que les interruptions régulières si elles ont lieu en ce moment
				// sauf si il y a un autre type évènement en cours
				" EI.isScheduled = 2 AND E.beginDate <= ".TIME." AND E.endDate >= ".TIME.
				" AND (SELECT count(*) FROM events E, events_isou EI".
				" WHERE EI.idService = ?".
				" AND EI.idEvent = E.idEvent".
				" AND (E.endDate IS NULL OR".
				" (E.beginDate <= ".TIME." AND E.endDate >= ".TIME."))) = 0".
				// fin_
				" ) OR (".
				// toutes les interruptions non régulières passées, en cours, à venir (dans la limite de $beginDate et $endDate)
				" EI.isScheduled < 2".
				" AND (E.endDate IS NULL OR".
				" ((E.beginDate BETWEEN ? AND ?".
				" OR E.endDate BETWEEN ? AND ?)".
				" AND (E.endDate-E.beginDate > ".$tolerance.")".
				" ))".
				// fin_
				" )".
				" ) ORDER BY E.beginDate DESC".
				" LIMIT ?";
		$event_records = $db->prepare($sql);
		$events = array();
		// if($event_records->execute(array($this->id, TIME, TIME, $beginDate, $endDate, $beginDate, $endDate, $tolerance, $limit))){
		if($event_records->execute(array($this->id, $this->id, $beginDate, $endDate, $beginDate, $endDate, $limit))){
			while($event = $event_records->fetch(PDO::FETCH_OBJ)){
				$event->serviceName = $this->nameForUsers;
				$event->state = $this->state;
				$event->isScheduled = 0; // nvm ?
				$event->type = NULL; // 'LastInterruption';
				$events[] = new IsouEvent($event);
			}
		}
		return $events;
	}

	/**
	 * 	@desc 	return all last interruptions events in array, except regular events
	 * 	@return array of IsouEvent
	 */
	public function getLastInterruptions($tolerance = 0, $limit = -1, $beginDate = '', $endDate = ''){
		global $db;

		if(empty($beginDate)){
			$beginDate = TIME-48*60*60;
		}else{
			$beginDate = intval($beginDate);
		}

		if(empty($endDate)){
			$endDate = TIME+48*60*60;
		}else{
			$endDate = intval($endDate);
		}

		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description, EI.isScheduled".
			" FROM events E, events_isou EI, events_description D".
			" WHERE EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.idService = :0".
			" AND ((EI.isScheduled < 2".
			" AND (E.beginDate BETWEEN :1 AND :2".
			" OR (E.endDate BETWEEN :3 AND :4 OR E.endDate IS NULL))".
			" AND (E.endDate - E.beginDate > ".$tolerance." OR E.endDate IS NULL))".
			" OR (EI.isScheduled = 3".
			" AND E.beginDate < :5".
			" AND (E.endDate > :6".
			" OR E.endDate IS NULL)))".
			" ORDER BY E.beginDate DESC".
			" LIMIT :7";
		$event_records = $db->prepare($sql);
		$events = array();
		if($event_records->execute(array($this->id, $beginDate, TIME, $beginDate, TIME, TIME, TIME, $limit))){
			while($event = $event_records->fetch(PDO::FETCH_OBJ)){
				$event->serviceName = $this->nameForUsers;
				$event->state = $this->state;
				$event->isScheduled = 0; // nvm ?
				$event->type = 'LastInterruption';
				$events[] = new IsouEvent($event);
			}
		}
		return $events;
	}

	/**
	 * 	@desc 	return current events
	 * 	@return array
	 */
	public function getClosedInterruption(){
		global $db;

		// le service est fermé
		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description, EI.isScheduled".
			" FROM events E, events_isou EI, events_description D".
			" WHERE EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.idService = :0".
			" AND EI.isScheduled = 3".
			" AND (E.endDate >= :1 OR E.endDate IS NULL)".
			" ORDER BY E.beginDate";
		$event_records = $db->prepare($sql);
		if($event_records->execute(array($this->getId(), TIME))){
			if($event = $event_records->fetch(PDO::FETCH_OBJ)){
				$event->serviceName = $this->nameForUsers;
				$event->state = $this->state;
				$event->isScheduled = 3;
				$event->type = NULL;
				return new IsouEvent($event);
			}
		}
		return array();
	}

	/**
	 * 	@desc 	return all regular interruptions
	 * 	@return array of IsouEvent
	 */
	public function getRegularInterruptions(){
		global $db;

		$sql = "SELECT E.idEvent, E.beginDate, E.endDate, EI.period, D.description, EI.isScheduled".
			" FROM events E, events_isou EI, events_description D".
			" WHERE EI.idEvent = E.idEvent".
			" AND EI.idEventDescription = D.idEventDescription".
			" AND E.typeEvent = 0".
			" AND EI.idService = :0".
			" AND EI.isScheduled = 2".
			" ORDER BY E.beginDate";
		$event_records = $db->prepare($sql);
		$events = array();
		if($event_records->execute(array($this->id))){
			while($event = $event_records->fetch(PDO::FETCH_OBJ)){
				$event->serviceName = $this->nameForUsers;
				$event->state = $this->state;
				$event->isScheduled = 2;
				$event->type = 'RegularInterruptions';
				$events[] = new IsouEvent($event);
			}
		}
		return $events;
	}


	/**
	 * 	@desc 	return formatted string of the object
	 * 	@return string
	 */
	public function __toString(){
		if(empty($this->nameForUsers)){
			return 'id: '.$this->id.' ; name: '.$this->name;
		}else{
			return 'id: '.$this->id.' ; name: '.$this->nameForUsers;
		}
	}

	/**
	*   @desc	Destruct instance
	*/
	public function __destruct() {
		// object destructed
	}
}
?>
