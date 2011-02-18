<?php

/**
*	@desc	   isou service class
*	@author 	CRI Rennes 2
*	@var		int	$id
*	@var		string	$name
*	@var		string	$nameForUsers
*	@var		int	$state
*	@var		string	$comment
*	@var		string	$categoryName
*	@var		IsouEvent array $events
*	@copyright	http://sam.zoy.org/wtfpl/
*	@version 	1.0
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
	public function __construct($id, $name = NULL, $nameForUsers = NULL, $url = NULL, $state = 3, $comment = NULL, $categoryName = NULL) {
		$this->id = $id;
		$this->name = stripslashes($name);
		$this->nameForUsers = stripslashes($nameForUsers);
		$this->url = stripslashes($url);
		$this->state = $state;
		$this->comment = stripslashes($comment);
		$this->categoryName = stripslashes($categoryName);
	}

	// Accessors GET
	public function getId()		 { return $this->id; }
	public function getName()		 { return $this->name; }
	public function getNameForUsers()	   { return $this->nameForUsers; }
	public function getUrl()	   { return $this->url; }
	public function getState()	  { return $this->state; }
	public function getComment()	  { return $this->comment; }
	public function getCategoryName()	{ return $this->categoryName; }

	// Accessors SET
	public function setState($state)	   { $this->state = $state; }
	public function setEvent($event)	   { $this->events[count($this->events)] = $event; }

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
		return is_array($this->events);
	}

	/**
	*   @desc		Return true if service is closed
	*   @return		boolean
	*/
	public function isClosed(){
		return $this->state==4;
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
	 * 	@desc Return html <ul> of RegularInterruption for this service
	 *  @return	string
	 */
	public function getRegularInterruption(){
		$i=0;
		$schedule = '';
		while(isset($this->events[$i])){
			if($this->events[$i]->getType() == 'RegularInterruptions'){
				if($this->events[$i]->getPeriod() == 7*24*60*60){
					// Tous les mois
					$schedule .= '<li>Tous les '.strftime('%d',$this->events[$i]->getBeginDate()).' du mois de '.strftime('%H:%M',$this->events[$i]->getBeginDate()).' à '.strftime('%H:%M',$this->events[$i]->getEndDate());
				}else if($this->events[$i]->getPeriod() == 7*24*60*60){
					// Toutes les semaines
					$schedule .= '<li>Tous les '.strftime('%A',$this->events[$i]->getBeginDate()).' de '.strftime('%H:%M',$this->events[$i]->getBeginDate()).' à '.strftime('%H:%M',$this->events[$i]->getEndDate());
				}else{
					// Tous les jours
					$schedule .= '<li>Tous les jours de '.strftime('%H:%M',$this->events[$i]->getBeginDate()).' à '.strftime('%H:%M',$this->events[$i]->getEndDate());
				}

				$description = $this->events[$i]->getDescription();
				if(empty($description)){
					$schedule .= '</li>';
				}else{
					$schedule .= ' ('.$description.')</li>';
				}
			}
			$i++;
		}

		if(empty($schedule)){
			return $schedule;
		}else{
			return '<ul class="regular">'.$schedule.'</ul>';
		}
	}

	/**
	 * 	@desc Return array with timestamps of begin and end date of the last interruption, and description
	 *  @return	array
	 */
	public function getLastInterruption(){
		$i = 0;
		$isFound = false;

		while(isset($this->events[$i]) && !$isFound){
			$isFound = $this->events[$i]->getType() == 'LastInterruption';
			$i++;
		}
		$i--;
		if($isFound == false){
			return array(NULL, NULL, NULL);
		}

		if($this->isClosed()){
			$description = $this->events[$i]->getDescription();
			if(empty($description)){
				$description = '';
			}else{
				$description = '('.$description.')';
			}

			if(is_null($this->events[$i]->getEndDate())){
				$beginDate = $this->events[$i]->getBeginDate();
				$endDate = NULL;
			}else{
				$beginDate = $this->events[$i]->getBeginDate();
				$endDate = $this->events[$i]->getEndDate();
			}
		}else{
			if(is_null($this->events[$i]->getBeginDate())){
				$beginDate = NULL;
			}else{
				$beginDate = $this->events[$i]->getBeginDate();
			}

			if(is_null($this->events[$i]->getEndDate())){
				if($beginDate == '&nbsp;'){
					$endDate = NULL;
				}else{
					$endDate = NULL;
				}
			}else{
				$endDate = $this->events[$i]->getEndDate();
			}

			if(is_null($this->events[$i]->getDescription())){
				$description = '&nbsp;';
				$description = NULL;
			}else{
				$description = stripslashes($this->events[$i]->getDescription());
			}
		}
		return array($beginDate, $endDate, $description);
	}

	/**
	 * 	@desc 	return formatted string of the next event
	 * 	@return string
	 */
	public function getNextEvent(){
		$i = 0;
		$isFound = false;
		while(isset($this->events[$i]) && !$isFound){
			$isFound = $this->events[$i]->getType() == 'NextInterruption';
			$i++;
		}
		$i--;
		if($isFound == false){
			return '';
		}

		$nextEvent = 'Interruption du '.strftime('%A %d %B %Y %H:%M',$this->events[$i]->getBeginDate()).' au '.strftime('%A %d %B %Y %H:%M',$this->events[$i]->getEndDate());

		$description = $this->events[$i]->getDescription();
		if(!empty($description)){
			$nextEvent .= ' ('.stripslashes($description).')';
		}

		return $nextEvent;
	}

	/**
	*   @desc	Destruct instance
	*/
	public function __destruct() {
		// object destructed
	}
}
?>
