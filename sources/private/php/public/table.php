<?php

	$TITLE = NAME.' - Liste';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_table.js');

	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/table.css');
	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/contact.css');

	// 2 jours avant
	$BEFORE = strftime('%Y-%m-%dT%H:%M', mktime(0,0,0)-(48*60*60));

	require PRIVATE_PATH.'/classes/isou/isou_service.class.php';
	require PRIVATE_PATH.'/classes/isou/isou_event.class.php';

	$services = array();
	$categories = array();
	$service_options[0] = '&nbsp;';

	$sql = "SELECT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, C.name AS category".
			" FROM services S, categories C".
			" WHERE C.idCategory = S.idCategory".
			" AND S.enable = 1".
			" AND S.visible = 1".
			" ORDER BY C.position, UPPER(S.nameForUsers)";
	if($service_records = $db->query($sql)){
		$categoryName = '';
		$i = 0;
		while($service = $service_records->fetchObject('IsouService')){
			// liste des services pour le template smarty {html_options}
			$service_options[$service->getId()] = $service->getNameForUsers();

			// changement de categorie
			if($categoryName !== $service->getCategoryName()){
				$categoryName = $service->getCategoryName();
				$categories[] = new stdClass();
				$categories[count($categories)-1]->name = $service->getCategoryName();
				$categories[count($categories)-1]->services = array();
			}

			// ajout des évènements
			if($service->isClosed() === TRUE){
				$service->closedEvent = $service->getClosedInterruption();
			}else{
				$service->lastEvent = $service->getAllEvents($TOLERANCE, 10, $BEFORE, strftime('%Y-%m-%dT%H:%M', TIME));// $service->getLastInterruptions($TOLERANCE, 1);
				$service->nextEvent = $service->getNextEvents(1);
				$service->regularInterruption = $service->getRegularInterruptions();
			}

			$categories[count($categories)-1]->services[] = $service;

			$i++;
		}
	}

	$smarty->assign('categories', $categories);

?>

