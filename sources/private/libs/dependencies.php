<?php

require_once PRIVATE_PATH.'/classes/isou/dependency_group.php';
require_once PRIVATE_PATH.'/classes/isou/dependency_group_content.php';

function get_dependency_group($id){
	global $DB;

	$sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, s.name AS service, dg.idmessage, dm.message".
			" FROM dependencies_groups dg, dependencies_messages dm, services s".
			" WHERE dm.id=dg.idmessage".
			" AND s.id=dg.idservice".
			" AND dg.id=?";
	$query = $DB->prepare($sql);
	$query->execute(array($id));

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');

	return $query->fetch();
}

function get_service_dependency_groups($idservice){
	global $DB;

	$sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message".
			" FROM dependencies_groups dg, dependencies_messages dm".
			" WHERE dm.id=dg.idmessage".
			" AND dg.idservice=?".
			" ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
	$query = $DB->prepare($sql);
	$query->execute(array($idservice));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');
}

function get_service_reverse_dependency_groups($idservice){
	global $DB;

	$sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage".
		" FROM dependencies_groups dg, dependencies_groups_content dgc".
		" WHERE dg.id = dgc.idgroup".
		" AND dgc.idservice=?".
		" ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
	$query = $DB->prepare($sql);
	$query->execute(array($idservice));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');
}

function get_dependency_groups_sorted_by_id(){
	global $DB, $FLAGS;

	$sql = "SELECT dg.id, dg.name, dg.groupstate".
		" FROM dependencies_groups dg".
		" WHERE dg.idservice=?".
		" ORDER BY dg.groupstate, UPPER(dg.name)";
	$query = $DB->prepare($sql);
	$query->execute(array($_GET['service']));

	$groups = array();
	while($group = $query->fetch(PDO::FETCH_OBJ)){
		$groups[$group->idgroup] = $group->name.' ('.$FLAGS[$group->groupstate]->title.')';
	}

	return $groups;
}

function get_dependency_group_content($idgroup, $idservice){
	global $DB;

	$sql = "SELECT dgc.idgroup, dgc.idservice, dgc.servicestate".
		" FROM dependencies_groups_content dgc".
		" WHERE dgc.idgroup=?".
		" AND dgc.idservice=?";
	$query = $DB->prepare($sql);
	$query->execute(array($idgroup, $idservice));

	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

	return $query->fetch();
}

function get_dependency_group_contents($idgroup){
	global $DB;

	$sql = "SELECT dgc.idgroup, dgc.idservice, dgc.servicestate".
		" FROM dependencies_groups_content dgc".
		" WHERE dgc.idgroup=?";
	$query = $DB->prepare($sql);
	$query->execute(array($idgroup));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');
}

// TODO: split this function
function get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($idservice){
	global $DB;

	$groups = array();

	$sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message".
			" FROM dependencies_groups dg, dependencies_messages dm".
			" WHERE dm.id=dg.idmessage".
			" AND dg.idservice=?".
			" ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
	$query = $DB->prepare($sql);
	$query->execute(array($idservice));
	$query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');

	while($group = $query->fetch()){
		if(!isset($groups[$group->groupstate])){
			$groups[$group->groupstate] = array();
		}

		// load content
		$sql = "SELECT dgc.idgroup, s.id, s.name, dgc.servicestate".
			" FROM dependencies_groups_content dgc, services s".
			" WHERE s.id=dgc.idservice".
			" AND dgc.idgroup=?".
			" ORDER BY dgc.servicestate DESC, s.name";
		$contents = $DB->prepare($sql);
		$contents->execute(array($group->id));
		$group->contents = $contents->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

		$groups[$group->groupstate][$group->id] = $group;
	}

	return $groups;
}

function get_dependency_message($id){
	global $DB;

	$sql = "SELECT idmessage, message".
			" FROM dependencies_messages".
			" WHERE idmessage=?";
	$query = $DB->prepare($sql);
	$query->execute(array($id));

	return $query->fetch(PDO::FETCH_OBJ);
}

?>
