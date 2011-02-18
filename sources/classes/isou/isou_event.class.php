<?php

/**
*	@desc	   isou event class
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
*	@copyright	http://sam.zoy.org/wtfpl/
*	@version 	1.0
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
	// prendre en parametre un object, directement
	public function __construct($id,$beginDate,$endDate,$period,$description,$serviceName,$state = NULL, $isScheduled = NULL, $type = NULL, $nagiosEvents = NULL) {
		$this->id = $id;
		$this->beginDate = $beginDate;
		$this->endDate = $endDate;
		$this->period = $period;
		$this->serviceName = stripslashes($serviceName);
		$this->state = $state;
		$this->isScheduled = intval($isScheduled);
		$this->type = $type;
		$this->nagiosEvents = $nagiosEvents;
		$this->description = $description;

		if(!is_null($state) && $isScheduled<2){
			/* fill static $array_events */
			if(is_null($endDate)){
				$endDate = mktime(23,59,59);
			}else{
				$date = getdate($endDate);
				$m = $date['mon'];
				$d = $date['mday'];
				$y = $date['year'];
				$endDate = mktime(23,59,59,$m,$d,$y);
			}
			while($beginDate <= $endDate){
				$formatBeginDate = strftime('%m/%d/%y',$beginDate);
				$i=0;
				$noExist = true;
				while(isset(self::$array_events[$formatBeginDate][$i]) && $noExist){
					// evite qu'il y ait 2 évènements le même jour
					if(substr(self::$array_events[$formatBeginDate][$i],0,-3) == $serviceName){
						$noExist = false;
						if($state < substr(self::$array_events[$formatBeginDate][$i],-1)){
							$state = substr(self::$array_events[$formatBeginDate][$i],-1);
						}
					}
					$i++;
				}

				if($noExist){
					if(isset(self::$array_events[$formatBeginDate])){
						self::$array_events[$formatBeginDate][count(self::$array_events[$formatBeginDate])] = $serviceName.'::'.$state;
					}else{
						self::$array_events[$formatBeginDate][0] = $serviceName.'::'.$state;
					}
				}else{
					$i--;
					self::$array_events[$formatBeginDate][$i] = $serviceName.'::'.$state;
				}
				$beginDate+=24*60*60;
			}
		}
	}

	// Accessors GET
	public function getId()		 { return $this->id; }
	public function getBeginDate()		 { return $this->beginDate; }
	public function getEndDate()	   { return $this->endDate; }
	public function getPeriod()	{ return $this->period; }
	public function getDescription()	{ return $this->description; }
	public function getServiceName()	{ return $this->serviceName; }
	public function getState()	{ return $this->state; }
	public function getScheduled()	{ return $this->isScheduled; }
	public function getType()	{ return $this->type; }
	public function getNagiosEvents()	{ return $this->nagiosEvents; }
	public function getArrayEvents()	  { return $this->array_events; }

	// Accessors SET
	public function setBeginDate($beginDate)		 { $this->beginDate = $beginDate; }
	public function setEndDate($endDate)	   { $this->endDate = $endDate; }


	/**
	*   @desc		Return a formated string with description and date
	*	@param		boolean		$rss : supprime les tags html si true
	*	@param		boolean		$old_description : affiche la raison de la panne des évènements passés si true
	*   @return		string
	*/
	public function Message($rss = false, $old_description = false) {

		if(is_null($this->description) || empty($this->description)){// == 'NULL'){
			$description = '';
		}else{
			$tab = explode('~',$this->description);
			$i = 0;

			if(isset($tab[$i]) && !empty($tab[$i])){
				$description = '<ul class="reason">';
				while(isset($tab[$i])){
					$description .= '<li>'.$tab[$i].'</li>';
					$i++;
				}
				$description .= '</ul>';
			}
		}

		if($this->isScheduled == 3){
			$beginDate = strftime('%A %d %B %Y',$this->beginDate);
			$endDate = strftime('%A %d %B %Y',$this->endDate);

			if(is_null($this->endDate)){
				$message = '<li>Service fermé depuis le '.$beginDate.'.'.$description.'</li>';
			}else{
				$message = '<li>Service fermé depuis le '.$beginDate.'. Réouverture le '.$endDate.'.'.$description.'</li>';
			}
		}else if($this->isScheduled == 2){
			$beginDate = strftime('%H:%M',$this->beginDate);
   			$endDate = strftime('%H:%M',$this->endDate);

			switch($this->period){
				case 86400 : $message = '<li>Le service est en maintenance quotidienne de '.$beginDate.' à '.$endDate.'. '.$description.'</li>';
							break;
				case 604800 : $message = '<li>Le service est en maintenance hebdomadaire de '.$beginDate.' à '.$endDate.'. '.$description.'</li>';
							break;
				default : $message = '<li>Le service est en maintenance de '.$beginDate.' à '.$endDate.'. '.$description.'</li>';
							break;
			}

		}else{
			$beginDate = strftime('%A %d %B %Y %H:%M',$this->beginDate);
   			$endDate = strftime('%A %d %B %Y %H:%M',$this->endDate);

			if(is_null($this->endDate)){
				$message = '<li><span class="currentEvent">Le service est actuellement perturbé depuis le '.$beginDate.'.'.$description.'</span></li>';
			}else{
				if(!is_null($this->endDate) && $this->endDate < TIME){
					// class="strike"
					if(strftime('%A%d%B',$this->beginDate) == strftime('%A%d%B',$this->endDate)){
						$message = '<li><span class="previousEvent">Le service a été perturbé le '.strftime('%A %d %B %Y',$this->beginDate).' de '.strftime('%H:%M',$this->beginDate).' à '.strftime('%H:%M',$this->endDate).'.</span>';
					}else{
						$message = '<li><span class="previousEvent">Le service a été perturbé du '.$beginDate.' au '.$endDate.'.</span>';
					}
					if($old_description){
						$message .= $description;
					}
					$message .= '</li>';
				}else{
					$message = '<li><span class="nextEvent">Le service sera perturbé du '.$beginDate.' au '.$endDate.'.'.$description.'</span></li>';
				}
			}
		}

		if($rss){
			$message = str_replace('<ul class="reason">',"\n",$message);
			$message = str_replace('</ul>','',$message);
			$message = str_replace('<li class="strike">',"\n",$message);
			$message = str_replace('<li>',"\n",$message);
			$message = str_replace('<s>','',$message);
			$message = str_replace('</s>','',$message);
			$message = str_replace('</li>','',$message);
			return $message;
		}else{
			return $message;
		}
	}

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
		for($i = 1;$i < count(self::$array_events);$i++){
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
	*   @desc	Destruct instance
	*/
	public function __destruct() {
		// object destructed
	}
}
?>
