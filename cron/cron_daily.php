<?php

$smarty = new Smarty();
$smarty->setTemplateDir(PRIVATE_PATH.'/html/');
$smarty->setCompileDir(PRIVATE_PATH.'/cache/smarty/');

//
// SUMMARY OF EVENTS
//
require PRIVATE_PATH.'/classes/isou/isou_service.class.php';
require PRIVATE_PATH.'/classes/isou/isou_event.class.php';

$sql = "SELECT DISTINCT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, C.name AS category".
	" FROM categories C, services S, events E, events_isou EI".
	" WHERE C.idCategory = S.idCategory".
	" AND EI.idEvent = E.idEvent".
	" AND E.typeEvent = 0".
	" AND S.idService = EI.idService".
	" AND S.enable = 1".
	" AND S.visible = 1".
	" ORDER BY C.position, UPPER(S.nameForUsers)";
$i=-1;
$categoryName = '';
$categories = array();
if($service_records = $DB->query($sql)){
	while($service = $service_records->fetchObject('IsouService')){

		$service->setEvents($service->getAllEvents($CFG['tolerance'], 10, $beginDateYesterday, $endDateYesterday));

		if($service->hasEvents() === TRUE){
			if($categoryName !== $service->getCategoryName()){
				$i++;
				$categoryName = $service->getCategoryName();
				$categories[$i] = new stdClass();
				$categories[$i]->name = $service->getCategoryName();
				$categories[$i]->services = array();
			}

			$service->stripName = strip_accents($service->getNameForUsers());

			$service->total = 0;
			foreach($service->getEvents() as $event){
				if($event->getScheduled() == 2){
					continue;
				}
				$endDate = $event->getEndDate();
				if($endDate === NULL){
					$endDate = TIME;
				}else{
					$endDate = strtotime($endDate);
				}
				$service->total += $endDate - strtotime($event->getBeginDate());
			}
			if($service->total === 0){
				if(!isset($categories[$i]->services[0])){
					unset($categories[$i]);
					$i--;
					$categoryName = '';
				}
				continue;
			}
			$service->total = secondstohuman($service->total);
			$categories[$i]->services[] = $service;
		}
	}
}
$smarty->assign('categories', $categories);

//
// REMOVED NAGIOS SERVICES
//

$arrayServices = array();
$arrayHosts = array();
$arrayNagios = array();

$handle = fopen(STATUSDAT_URL, "r");
if ($handle) {
	while (!feof($handle)) {
		$tp =  trim(fgets($handle, 4096));
		if(preg_match('#hoststatus \{|servicestatus \{#',$tp)){
			$tag = substr($tp,0,-2);
			$continue=true;
			$host_name = "";
			$service_description = "";

			while(!feof($handle) && $continue){
				$tp =  trim(fgets($handle, 4096));
				if(!preg_match('#}#',$tp)){
					if(preg_match('#host_name=|service_description=#',$tp)){
						$split = explode('=',$tp);
						if($split[0] == 'host_name'){
							$host_name=$split[1];
						}else{
							$service_description=$split[1];
						}
					}
				}else{
					$continue=false;
				}
			}

			if($tag == 'servicestatus'){
				$host_name = $service_description.'@'.$host_name;
			}

			$sql = "SELECT idService, nameForUsers, idCategory".
					" FROM services".
					" WHERE name = '".$host_name."'".
					" AND nameForUsers IS NULL";
			$DB->query($sql);

			if($nameForUsers = $DB->query($sql)){
				$nameForUsers = $nameForUsers->fetch();
			}

			if(is_null($nameForUsers[0])){
				if($tag == 'servicestatus'){
					$arrayServices[count($arrayServices)] = $host_name;
				}else{
					$arrayHosts[count($arrayHosts)] = $host_name;
				}
			}

			$arrayNagios[count($arrayNagios)] = $host_name;
		}
	}
	fclose($handle);
}

sort($arrayServices);
sort($arrayHosts);

$sql = "SELECT S.idService, S.name, S.nameForUsers, S.visible".
		" FROM services S".
		" WHERE S.nameForUsers IS NULL".
		" ORDER BY UPPER(S.name)";
$services = $DB->query($sql);

$i = 0;
$nagiosServices = array();
while($service = $services->fetchObject()){
	if(!in_array(stripslashes($service->name), $arrayNagios)){
		$nagiosServices[] = $service->name;
	}
	$i++;
}

$smarty->assign('nagiosServices', $nagiosServices);

// SERVICES FERMES
$sql = "SELECT S.idService, S.name, S.nameForUsers, S.state".
	" FROM services S".
	" WHERE S.readonly = 1".
	" AND S.name = 'Service final'".
	" ORDER BY S.nameForUsers";
$services = $DB->prepare($sql);
$services->execute();

$forcedservices = $services->fetchAll(PDO::FETCH_OBJ);

// load states
require PRIVATE_PATH.'/libs/states.php';
$STATES = get_states();

$smarty->assign('forcedservices', $forcedservices);
$smarty->assign('STATES', $STATES);

$subject = 'Rapport ISOU du '.strftime('%A %e %B', $beginDateYesterday);
$message = $smarty->fetch('mail_cron_daily_text.tpl');

foreach($CFG['admin_mails'] as $mail){
	if(filter_var($mail, FILTER_VALIDATE_EMAIL) !== FALSE){
		isoumail($mail, $subject, $message);
	}
}

?>
