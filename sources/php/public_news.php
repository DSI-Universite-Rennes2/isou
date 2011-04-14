<?php

function get_parents($idChild){
	$parents = array();

	try {
		$dbr = new PDO(DB_PATH, '', '');
		$sql = "SELECT DISTINCT D.idServiceParent, S.name, S.nameForUsers, S.state".
		" FROM dependencies AS D, services AS S".
		" WHERE S.idService = D.idServiceParent".
		" AND D.idService = :0".
		" ORDER BY UPPER(S.name), UPPER(S.nameForUsers)";

		$services = $dbr->prepare($sql);
		$services->execute(array($idChild));
		while($service = $services->fetch(PDO::FETCH_OBJ)){
			$tmpParents = get_parents($service->idServiceParent);
			if(count($tmpParents) > 0){
				$service->parents = $tmpParents;
			}
			$parents[] = $service;
		}

	} catch (PDOException $e) {
		add_log(LOG_FILE, phpCAS::getUser(), 'ERROR_DB', $e->getMessage());
	}

	// close pdo connection
	$dbr = null;

	return $parents;
}

if(class_exists('IsouService') === FALSE){
	// used by news page
	require BASE.'/classes/isou/isou_service.class.php';
	require BASE.'/classes/isou/isou_event.class.php';

	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>';
	$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_news.js"></script>';
	$title = NAME.' - Actualité';
	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/news.css" media="screen" />';

	// 2 jours avant
	$BEFORE = mktime(0,0,0)-(48*60*60);
	// 2 jours apres
	$AFTER = mktime(0,0,0)+(48*60*60);
}else{
	// used by calendar page
	$BEFORE = TIMESTAMP_OF_FIRST_CALENDAR_DAY;
	$AFTER = TIMESTAMP_OF_LAST_CALENDAR_DAY;
	$calendar = TRUE;
}

$sql = "SELECT EI.shortText, EI.longText".
		" FROM events_info EI, events E".
		" WHERE E.idEvent = EI.idEvent".
		" AND (E.beginDate > ?".
		" OR E.endDate IS NULL)";
$query = $db->prepare($sql);
$messages = array();
if($query->execute(array($BEFORE))){
	while($message = $query->fetchObject()){
		$messages[] = $message;
	}
}

$services = array();

// recupere tous les services dans la bdd qui ont un évènement entre le lundi de la semaine d'avant, et 35 jours après ce lundi
$sql = "SELECT DISTINCT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, C.name AS category".
	" FROM categories C, services S, events E, events_isou EI".
	" WHERE C.idCategory = S.idCategory".
	" AND EI.idEvent = E.idEvent".
	" AND E.typeEvent = 0".
	" AND S.idService = EI.idService".
	" AND S.enable = 1".
	" AND S.visible = 1".
	" ORDER BY C.position, UPPER(S.nameForUsers)";
$i=0;
$categoryName = '';
$categories = array();
if($service_records = $db->query($sql)){
	while($service = $service_records->fetchObject('IsouService')){

		if(isset($calendar)){
			$service->setEvents($service->getScheduledEvents($TOLERANCE, -1, TIMESTAMP_OF_FIRST_CALENDAR_DAY, TIMESTAMP_OF_LAST_CALENDAR_DAY));
		}else{
			$service->setEvents($service->getAllEvents($TOLERANCE, 10, $BEFORE, $AFTER));
		}

		if($service->hasEvents() === TRUE){
			if($categoryName !== $service->getCategoryName()){
				$categoryName = $service->getCategoryName();
				$categories[] = new stdClass();
				$categories[count($categories)-1]->name = $service->getCategoryName();
				$categories[count($categories)-1]->services = array();
			}

			$service->stripName = strip_accents($service->getNameForUsers());
			$service->parents = get_parents($service->getId());

			$categories[count($categories)-1]->services[] = $service;
			$i++;
		}else{
			unset($services[$i]);
		}
	}
}

$smarty->assign('categories', $categories);
$smarty->assign('messages', $messages);

?>
