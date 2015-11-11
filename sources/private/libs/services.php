<?php

use UniversiteRennes2\Isou;

require_once PRIVATE_PATH.'/classes/isou/service.php';

function get_services_by_category($idcategory){
	global $DB;

	$sql = "SELECT s.idservice, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idtype, s.idcategory".
			" FROM services s".
			" WHERE s.idcategory=?";
	$query = $DB->prepare($sql);
	$query->execute(array($idcategory));

	return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
}

?>
