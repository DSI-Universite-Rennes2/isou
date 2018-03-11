<?php

require_once PRIVATE_PATH.'/classes/isou/event.php';

function get_event($id){
	global $DB;

	$sql = "SELECT e.id, e.begindate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, e.idservice".
			" FROM events e, events_descriptions ed".
			" WHERE e.ideventdescription = e.ideventdescription".
			" AND e.id=?";
	$query = $DB->prepare($sql);
	$query->execute(array($id));

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');

	return $query->fetch();
}


/**
* @param array $options Array in format:
*	after			=> DateTime
*	before			=> DateTime
*	idservice		=> int
*	one_record		=> bool
*	regular			=> bool
*	service_type	=> int : index key from UniversiteRennes2\Isou\Service::$TYPES
*	since			=> DateTime
*	finished		=> bool
*	state			=> int : index key from UniversiteRennes2\Isou\State::$STATES
*	tolerance		=> int : seconds
*	type			=> int : index key from UniversiteRennes2\Isou\Event::$TYPES
*	sort			=> Array of strings
*
* @return array of UniversiteRennes2\Isou\Events
*/

function get_events($options = array()){
	global $DB;

	$params = array();
	$conditions = array();

	$sql = "SELECT e.id, e.begindate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, e.idservice, s.name AS service_name".
			" FROM events e, events_descriptions ed, services s".
			" WHERE s.id=e.idservice".
			" AND ed.id=e.ideventdescription";

	// after options
	if(isset($options['after']) && $options['after'] instanceof DateTime){
		$sql .= " AND e.begindate >= ?";
		$params[] = $options['after']->format('Y-m-d\TH:i');
	}

	// before options
	if(isset($options['before']) && $options['before'] instanceof DateTime){
		$sql .= " AND e.begindate < ?";
		$params[] = $options['before']->format('Y-m-d\TH:i');
	}

	// idservice options
	if(isset($options['idservice']) && ctype_digit($options['idservice'])){
		$sql .= " AND s.id = ?";
		$params[] = $options['idservice'];
	}

	// regular options
	if(isset($options['regular'])){
		if($options['regular'] === TRUE){
			$sql .= " AND e.period IS NOT NULL";
		}else{
			$sql .= " AND e.period IS NULL";
		}
	}

	// service_type options
	if(isset($options['service_type'], UniversiteRennes2\Isou\Service::$TYPES[$options['service_type']])){
		$sql .= " AND s.idtype=?";
		$params[] = $options['service_type'];
	}

	// since options
	if (isset($options['since']) === true) {
		$sql .= " AND (e.enddate IS NULL OR e.begindate >= ?)";
		if ($options['since'] instanceof DateTime) {
			$params[] = $options['since']->format('Y-m-d\TH:i:s');
		} else {
			$params[] = $options['since'];
		}
	}

	// closed option
	if(isset($options['finished'])){
		if ($options['finished'] === true) {
			$sql .= " AND e.enddate IS NOT NULL";
		} else {
			$sql .= " AND e.enddate IS NULL";
		}
	}

	// state options
	if(isset($options['state'], UniversiteRennes2\Isou\State::$STATES[$options['state']])){
		$sql .= " AND e.state=?";
		$params[] = $options['state'];
	}

	// tolerance options
	if(isset($options['tolerance']) && ctype_digit($options['tolerance']) && $options['tolerance'] > 0){
		$sql .= " AND".
			" (".
			" (e.enddate IS NULL)".// AND (strftime('%s', '".STR_TIME."') - strftime('%s', e.begindate)) > ".$options['tolerance'].")".
			" OR".
			" ((strftime('%s', e.enddate) - strftime('%s', e.begindate)) > ".$options['tolerance'].")".
			" )";
	}

	// type options
	if(isset($options['type'], UniversiteRennes2\Isou\Event::$TYPES[$options['type']])){
		$sql .= " AND e.type=?";
		$params[] = $options['type'];
	}

	// sort options
	if(isset($options['sort']) && is_array($options['sort'])){
		$sql .= " ORDER BY ".implode(', ', $options['sort']);
	}else{
		$sql .= " ORDER BY e.begindate, e.enddate";
	}

	$query = $DB->prepare($sql);
	$query->execute($params);

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');

	if(isset($options['one_record'])){
		$events = $query->fetchAll();
		if(isset($events[0])){
			return $events[0];
		}else{
			return FALSE;
		}
	}

	return $query->fetchAll();
}


function get_events_by_type($since=NULL, $type=NULL, $servicetype=NULL, $tolerance=TRUE){
	global $CFG, $DB;

	$params = array();

	if($type === NULL){
		$sql_type = "";
	}else{
		$sql_type = " AND e.type = ?";
		$params[] = $type;
	}

	if($servicetype === NULL){
		$sql_servicetype = "";
	}else{
		$sql_servicetype = " AND s.idtype = ?";
		$params[] = $servicetype;
	}

	if($since === NULL){
		$sql_tolerance = "";
	}else{
		$params[] = $since;
		if($tolerance === TRUE){
			$sql_tolerance = " AND ((e.enddate > ? AND strftime('%s', enddate)-strftime('%s', begindate) > ?) OR e.enddate IS NULL)";
			$params[] = $CFG['tolerance'];
		}else{
			$sql_tolerance = " AND (e.enddate > ? OR e.enddate IS NULL)";
		}
	}

	$sql = "SELECT e.id, e.begindate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, s.id, s.name".
		" FROM events e, services s, events_descriptions ed".
		" WHERE s.id = e.idservice".
		" AND ed.id = e.ideventdescription".
		" AND s.enable = 1".$sql_type.$sql_servicetype.$sql_tolerance.
		" ORDER BY e.beginDate DESC";
	$query = $DB->prepare($sql);
	$query->execute($params);

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');
}
