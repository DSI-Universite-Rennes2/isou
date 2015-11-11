<?php

require_once PRIVATE_PATH.'/classes/isou/dependency_group.php';

function get_service_dependency_groups($idservice){
	global $DB;

	$sql = "SELECT dg.idgroup, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message".
			" FROM dependencies_groups dg, dependencies_messages dm".
			" WHERE dm.idmessage=dg.idmessage".
			" AND dg.idservice=?".
			" ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
	$query = $DB->prepare($sql);
	$query->execute(array($idservice));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');
}

function get_service_reverse_dependency_groups($idservice){
	global $DB;

	$sql = "SELECT dg.idgroup, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage".
		" FROM dependencies_groups dg, dependencies_groups_content dgc".
		" WHERE dg.idgroup = dgc.idgroup".
		" AND dgc.idservice=?".
		" ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
	$query = $DB->prepare($sql);
	$query->execute(array($idservice));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');
}

?>
