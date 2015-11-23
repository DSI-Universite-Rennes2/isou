<?php

require_once PRIVATE_PATH.'/classes/isou/event.php';

function get_event($id){
	global $DB;

	$sql = "SELECT e.idevent, e.begindate, e.enddate, e.state, e.type, e.period, ed.ideventdescription, ed.description, e.idservice".
			" FROM events e, events_descriptions ed".
			" WHERE ed.ideventdescription = e.ideventdescription".
			" AND e.idevent=?";
	$query = $DB->prepare($sql);
	$query->execute(array($id));

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');

	return $query->fetch();
}

function get_events($begindate_before=NULL, $begindate_after=NULL, $enddate_before=NULL, $enddate_after=NULL, $tolerance=TRUE){
	global $DB;

	$params = $conditions = array();

	$sql = "SELECT idevent, begindate, enddate, state, type, period, ideventdescription, idservice".
			" FROM events";

	if($tolerance === TRUE){
		$tolerance_condition = " AND strftime('%s', endDate)-strftime('%s', beginDate) > ?";
		$params[] = $CFG['tolerance'];
	}else{
		$tolerance_condition = '';
	}

	if($begindate_before !== NULL){
		$begin_conditions[] = " begindate <= ?";
		$params[] = $begindate_before;
	}

	if($begindate_after !== NULL){
		$begin_conditions[] = " begindate >= ?";
		$params[] = $begindate_after;
	}
	$begin_conditions = implode(' AND', $begin_conditions);

	if($enddate_before !== NULL){
		$end_conditions[] = " enddate <= ?";
		$params[] = $enddate_before;
	}

	if($enddate_after !== NULL){
		$end_conditions[] = " enddate >= ?";
		$params[] = $enddate_after;
	}
	$end_conditions = implode(' AND', $end_conditions);

	if(isset($end_conditions[0])){
		$sql .= " WHERE (enddate IS NULL OR (($begin_conditions AND $end_conditions) $tolerance_condition))";
	}elseif(isset($begin_conditions[0])){
		$sql .= " WHERE $begin_condition $tolerance_condition";
	}else{
		$sql .= str_replace('AND', 'WHERE', $tolerance_condition);
	}

	$sql .= " ORDER BY begindate, enddate";

	$query = $DB->prepare($sql);
	$query->execute($params);

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');

	return $query->fetchAll();
}

function get_isou_events($begindate_before=NULL, $begindate_after=NULL, $enddate_before=NULL, $enddate_after=NULL, $tolerance=TRUE){
	global $DB;

	$params = $conditions = array();

	$sql = "SELECT e.idevent, e.begindate, e.enddate, e.state, e.type, e.period, e.ideventdescription, e.idservice".
			" FROM events e, services s".
			" WHERE s.idservice = e.idservice".
			" AND s.idtype = ?";
	$params[] = Service::SERVICETYPE_ISOU;

	if($tolerance === TRUE && isset($_SESSION['tolerance'])){
		$tolerance_condition = " AND strftime('%s', e.endDate)-strftime('%s', e.beginDate) > ?";
		$params[] = $_SESSION['tolerance'];
	}else{
		$tolerance_condition = '';
	}

	// $begindate_before
	if($begindate_before !== NULL){
		$begin_conditions[] = " e.begindate <= ?";
		$params[] = $begindate_before;
	}

	if($begindate_after !== NULL){
		$begin_conditions[] = " e.begindate >= ?";
		$params[] = $begindate_after;
	}

	if(isset($begin_conditions[0])){
		$begin_conditions = implode(' AND', $begin_conditions);
	}else{
		$begin_conditions = '';
	}

	// $enddate_before
	if($enddate_before !== NULL){
		$end_conditions[] = " e.enddate <= ?";
		$params[] = $enddate_before;
	}

	if($enddate_after !== NULL){
		$end_conditions[] = " e.enddate >= ?";
		$params[] = $enddate_after;
	}

	if(isset($end_conditions[0])){
		$end_conditions = implode(' AND', $end_conditions);
	}else{
		$end_conditions = '';
	}

	if(isset($end_conditions[0], $begin_conditions[0])){
		$sql .= " WHERE (e.enddate IS NULL OR (($begin_conditions AND $end_conditions) $tolerance_condition))";
	}elseif(isset($begin_conditions[0])){
		$sql .= " WHERE $begin_condition $tolerance_condition";
	}else{
		$sql .= str_replace('AND', 'WHERE', $tolerance_condition);
	}

	$sql .= " ORDER BY e.begindate, e.enddate";

	$query = $DB->prepare($sql);
	$query->execute($params);

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');
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

	$sql = "SELECT e.idevent, e.begindate, e.enddate, e.state, e.type, e.period, ed.ideventdescription, ed.description, s.idservice, s.name".
		" FROM events e, services s, events_descriptions ed".
		" WHERE s.idservice = e.idservice".
		" AND ed.ideventdescription = e.ideventdescription".
		" AND s.enable = 1".$sql_type.$sql_servicetype.$sql_tolerance.
		" ORDER BY e.beginDate DESC";
	$query = $DB->prepare($sql);
	$query->execute($params);

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');
}

?>
