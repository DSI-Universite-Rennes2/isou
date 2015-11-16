<?php

require_once PRIVATE_PATH.'/classes/isou/service.php';

function get_service($id, $type=NULL){
	global $DB;

	if($type === NULL){
		$sql_condition = '';
		$params = array($id);
	}else{
		$sql_condition = " AND s.idtype=?";
		$params = array($id, $type);
	}

	$sql = "SELECT s.idservice, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idtype, s.idcategory".
			" FROM services s".
			" WHERE s.idservice=?".$sql_condition;
	$query = $DB->prepare($sql);
	$query->execute($params);

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');

	return $query->fetch();
}

function get_services($type=NULL){
	global $DB;

	if($type === NULL){
		$sql_condition = '';
		$params = array();
	}else{
		$sql_condition = " WHERE s.idtype=?";
		$params = array($type);
	}

	$sql = "SELECT s.idservice, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idtype, s.idcategory".
			" FROM services s".$sql_condition.
			" ORDER BY UPPER(s.name)";
	$query = $DB->prepare($sql);
	$query->execute($params);

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
}

function get_services_sorted_by_id($type=NULL){
	global $DB;

	if($type === NULL){
		$sql = "SELECT idservice, name FROM services ORDER BY UPPER(name)";
		$params = array();
	}else{
		$sql = "SELECT idservice, name FROM services WHERE idtype=? ORDER BY UPPER(name)";
		$params = array($type);
	}
	$query = $DB->prepare($sql);
	$query->execute($params);
	return $query->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE);
}

function get_services_sorted_by_idtype(){
	global $CFG, $DB;

	$sql = "SELECT s.idservice, s.name, s.idtype".
		" FROM services s".
		" ORDER BY UPPER(s.name)";
	$query = $DB->prepare($sql);
	$query->execute();

	$services = array();
	if($CFG['nagios_statusdat_enable'] === '1'){
		$services['Services Nagios'] = array();
	}

	if($CFG['shinken_thruk_enable'] === '1'){
		$services['Services Shinken'] = array();
	}

	$services['Services ISOU'] = array();

	while($service = $query->fetch(PDO::FETCH_OBJ)){
		if($service->idtype === UniversiteRennes2\Isou\Service::TYPE_NAGIOS_STATUSDAT){
			if(isset($services['Services Nagios'])){
				$services['Services Nagios'][$service->idservice] = $service->name;
			}
		}elseif($service->idtype === UniversiteRennes2\Isou\Service::TYPE_SHINKEN_THRUK){
			if(isset($services['Services Shinken'])){
				$services['Services Shinken'][$service->idservice] = $service->name;
			}
		}else{
			$services['Services ISOU'][$service->idservice] = $service->name;
		}
	}

	return $services;
}

function get_isou_services_sorted_by_idtype(){
	global $DB;

	$sql = "SELECT s.idservice, s.name".
		" FROM services s".
		" WHERE s.idtype=?".
		" ORDER BY UPPER(s.name)";
	$query = $DB->prepare($sql);
	$query->execute(array(UniversiteRennes2\Isou\Service::TYPE_ISOU));

	return $query->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE);
}

function get_services_by_category($idcategory){
	global $DB;

	$sql = "SELECT s.idservice, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idtype, s.idcategory".
			" FROM services s".
			" WHERE s.idcategory=?";
	$query = $DB->prepare($sql);
	$query->execute(array($idcategory));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
}

function get_services_by_dependencies_group($idgroup){
	global $DB;

	$sql = "SELECT s.idservice, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idtype, s.idcategory, dgc.servicestate".
			" FROM services s, dependencies_groups_content dgc".
			" WHERE s.idservice=dgc.idservice".
			" AND dgc.idgroup=?".
			" ORDER BY UPPER(s.name)";
	$query = $DB->prepare($sql);
	$query->execute(array($idgroup));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
}


?>
