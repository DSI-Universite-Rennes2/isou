<?php

/**
*	@desc		isou event class
*	@author 	CRI Rennes 2
*	@var		int	$id
*	@var		timestamp	$beginDate
*	@var		timestamp	$endDate
*	@var		string	$description
*	@var		string	$serviceName
* 	@var		int	$state
* 	@var		int	$isScheduled
* 	@var		string	$type
* 	@var		IsouEvent array	$nagiosEvents
* 	@var		static array $array_events
*	@copyright	http://www.gnu.org/licenses/gpl-2.0.html
*	@version 	2.0
*	@since		1.0
*/

class IsouEvent {

	protected $id;
	protected $beginDate;
	protected $endDate;
	protected $period;
	protected $description;
	protected $serviceName;
	protected $state;
	protected $isScheduled;
	protected $type;
	protected $nagiosEvents;

	public static $array_events;

	/**
	*	@desc		Constructor with params
	*	@param		int $id unique ID
	*	@param		timestamp $beginDate
	* 	@param		timestamp $endDate
	* 	@param		int $period						// 86400 for daily regular events, 604800 for weekly regular events
	*  	@param		string $description				// description of event
	*  	@param		string $serviceName
	*  	@param		int $state						// 0 for OK, 1 for orange, 2 for stopped, 3 for unknown state, 4 for closed
	*  	@param		int $isScheduled				// 0 for unscheduled events, 1 for scheduled events, 2 for regular events, 3 for closed services
	*  	@param		string $type					// NULL, 'RegularInterruptions', 'LastInterruption' or 'NextInterruption'
	*  	@param		IsouEvent array $nagiosEvents	// array of events from nagios event
	*	@return		void
	*/

	public function __construct(){ // $id,$beginDate,$endDate,$period,$description,$serviceName,$state = NULL, $isScheduled = NULL, $type = NULL, $nagiosEvents = NULL) {
		$countArgs = func_num_args();
		$args = func_get_args();

		if($countArgs === 0 && isset($this->idService)){
			// instanciation depuis un appel implicite de pdo (fetchObject)
			$this->id = intval($this->idEvent);
			$this->beginDate = gmstrftime('%Y-%m-%dT%H:%M', $this->beginDate);
			$this->endDate = $this->endDate;
			$this->period = intval($this->period);
			$this->serviceName = $this->serviceName;
			$this->state = $this->state;
			$this->isScheduled = intval($this->isScheduled);
			if(isset($args[0]->type)){
				$this->type = $args[0]->type;
			}else{
				$this->type = NULL;
			}
			$this->nagiosEvents = array();
			$this->description = $this->description;
		}else if($countArgs === 1 && is_object($args[0])){
			// instanciation depuis un appel explicite __construct(param) (stdClass ou pdo)
			$this->id = intval($args[0]->idEvent);
			$this->beginDate = gmstrftime('%Y-%m-%dT%H:%M', $args[0]->beginDate);
			$this->endDate = $args[0]->endDate;
			$this->period = intval($args[0]->period);
			$this->serviceName = $args[0]->serviceName;
			$this->state = $args[0]->state;
			$this->isScheduled = intval($args[0]->isScheduled);
			if(isset($args[0]->type)){
				$this->type = $args[0]->type;
			}else{
				$this->type = NULL;
			}
			$this->nagiosEvents = array();
			$this->description = $args[0]->description;
		}else if($countArgs === 10){
			// instanciation "manuelle" depuis un appel explicite __construct(param1, param2, etc...)
			$this->id = intval($args[0]);
			$this->beginDate = gmstrftime('%Y-%m-%dT%H:%M', $args[1]);
			$this->endDate = $args[2];
			$this->period = intval($args[3]);
			$this->serviceName = $args[4];
			$this->state = intval($args[5]);
			$this->isScheduled = $args[6];
			$this->type = $args[7];
			$this->nagiosEvents = array();
			$this->description = $args[9];
		}else{
			$this->__destruct();
			new Exception('Pas assez de paramètres');
			$debug = debug_backtrace();
			die('Class IsouEvent (__construct) : pas assez de paramètres, fichier '.$debug[0]['file'].' ligne '.$debug[0]['line']);
		}

		if(empty($this->description)){
			$this->description = NULL;
		}

		if(empty($this->endDate)){
			$this->endDate = NULL;
		}else{
			$this->endDate = gmstrftime('%Y-%m-%dT%H:%M', $this->endDate);
		}

		if(!is_null($this->state) && $this->isScheduled < 2){
			/* fill static $array_events */
			$beginDate = $this->beginDate;

			if(is_null($this->endDate)){
				$endDate = mktime(23,59,59);
			}else{
				$endDate = $this->endDate;
				/*$date = getdate($this->endDate);
				$m = $date['mon'];
				$d = $date['mday'];
				$y = $date['year'];
				$endDate = mktime(23,59,59,$m,$d,$y);*/
			}

			while($beginDate <= $endDate){
				$formatBeginDate = substr($beginDate, 0, 10); // YYYY-MM-DD
				$i=0;
				$noExist = true;
				while(isset(self::$array_events[$formatBeginDate][$i]) && $noExist){
					// evite qu'il y ait 2 évènements le même jour
					if(substr(self::$array_events[$formatBeginDate][$i],0,-3) == $this->serviceName){
						$noExist = false;
						if($this->state < substr(self::$array_events[$formatBeginDate][$i],-1)){
							$this->state = substr(self::$array_events[$formatBeginDate][$i],-1);
						}
					}
					$i++;
				}

				if($noExist){
					if(isset(self::$array_events[$formatBeginDate])){
						self::$array_events[$formatBeginDate][count(self::$array_events[$formatBeginDate])] = $this->serviceName.'::'.$this->state;
					}else{
						self::$array_events[$formatBeginDate][0] = $this->serviceName.'::'.$this->state;
					}
				}else{
					$i--;
					self::$array_events[$formatBeginDate][$i] = $this->serviceName.'::'.$this->state;
				}
				$beginDate+=24*60*60;
			}
		}
	}

	// Accessors GET
	public function getId()						{ return $this->id; }
	public function getBeginDate()				{ return $this->beginDate; }
	public function getEndDate()				{ return $this->endDate; }
	public function getPeriod()					{ return $this->period; }
	public function getDescription()			{ return $this->description; }
	public function getServiceName()			{ return $this->serviceName; }
	public function getState()					{ return $this->state; }
	public function getScheduled()				{ return $this->isScheduled; }
	public function getType()					{ return $this->type; }
	public function getArrayEvents()			{ return $this->array_events; }

	// Accessors SET
	public function setBeginDate($beginDate)	{ $this->beginDate = $beginDate; }
	public function setEndDate($endDate)		{ $this->endDate = $endDate; }


	/**
	*   @desc		Sort static $array_events
	*   @return		string
	*/
	public function sortArrayEvents() {
		$today = strftime('%m/%d/%y',TIME);

		if(isset(self::$array_events[$today][0])){
			$newArrayEvents = self::$array_events[$today][0];
		}

		// self::$array_events[$formatBeginDate][0] = $serviceName.'::'.$state;
		$count = count(self::$array_events);
		for($i = 1;$i < $count;$i++){
			$j = 0;
			$found = false;

			while(isset($newArrayEvents[$j]) && $found){
				$explode1 = explode('::',self::$array_events[$today][$i]);
				$explode2 = explode('::',self::$newArrayEvents[$j]);

				if($explode1[1] < $explode2[1] || ($explode1[1] < $explode2[1] && $explode1[0] <= $explode2[0])){
					$k = $j;
					$tmp = $newArrayEvents[$k];
					$newArrayEvents[$k] = self::$array_events[$today][$i];
					$k++;

					while(isset($newArrayEvents[$k+1])){
						$newArrayEvents[$k] = $tmp;
						$tmp = $newArrayEvents[$k+1];
						$k++;
					}
					$newArrayEvents[$k] = $tmp;
					$found == true;
				}
				$j++;
			}
		}
	}

	/**
	*   @desc		find recursively all nagios events from current event
	*   @return		array of IsouEvent()
	*/
	private function getNagiosEvents_recursive($idService){
		global $DB, $CFG;

		$nagiosEvents = array();
		$tolerance = $CFG['tolerance'].' seconds';

		// si l'evenement n'est pas en cours, on recup tous les evenements nagios
		// on met +/- 10 secondes de tolérence
		$sql = "SELECT DISTINCT s.name, s.nameForUsers, strftime('%s',E.beginDate) AS beginDate, strftime('%s',E.endDate) AS endDate, en.state".
				" FROM events e, events_nagios en, services s, dependencies d".
				" WHERE s.idService = en.idService".
				" AND e.idEvent = en.idEvent".
				" AND d.idServiceParent = s.idService".
				" AND d.idService = ?".
				" AND e.beginDate >= strftime('%Y-%m-%dT%H:%M', ?, ?)".
				" AND e.endDate <= strftime('%Y-%m-%dT%H:%M', ?, ?)";
		$nagios_records = $DB->prepare($sql);
		if($nagios_records->execute(array($idService, $this->beginDate, '-'.$tolerance, $this->endDate, '+'.$tolerance))){
			$j = 0;
			while($nagios_record = $nagios_records->fetchObject()){
				// TODO: à déplacer vers le constructeur...
				$nagios_record->isScheduled = 0;
				$nagios_record->description = '';
				$nagios_record->idEvent = 0;
				$nagios_record->period = 0;
				if(empty($nagios_record->nameForUsers)){
					$nagios_record->serviceName = $nagios_record->name;
				}else{
					$nagios_record->serviceName = $nagios_record->nameForUsers;
				}
				$nagiosEvents[] = new IsouEvent($nagios_record);
			}
		}

		// tous les services Isou qui sont parents d'un autre service Isou
		$sql = "SELECT DISTINCT d.idServiceParent, s.nameForUsers".
				" FROM dependencies d, services s".
				" WHERE s.idService = d.idServiceParent".
				" AND d.idService = ".$idService.
				" AND s.nameForUsers IS NOT NULL";
		// echo $sql;
		$dep_records = $DB->prepare($sql);
		if($dep_records->execute(array())){
			while($dep = $dep_records->fetchObject()){
				$nagiosEvents[] = array($dep->nameForUsers, $this->getNagiosEvents_recursive($dep->idServiceParent));
			}
		}

		return $nagiosEvents;
	}


	/**
	*   @desc		find all nagios events from current event
	*   @return		array of IsouEvent()
	*/
	public function getNagiosEvents($idService){

		$nagiosEvents = array();

		if(!empty($this->endDate)){
			$nagiosEvents = $this->getNagiosEvents_recursive($idService);
		}

		return $nagiosEvents;
	}

	/**
	*   @desc	Destruct instance
	*/
	public function __destruct() {
		// object destructed
	}
}
?>
