<?php

require BASE.'/classes/smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = BASE.'/html/';
$smarty->compile_dir = BASE.'/classes/smarty/compile/';

//
// STATISTICS
//
$yesterday = explode(' ', strftime('%m %d %Y', TIME-(24*60*60)));
$beginDateYesterday = mktime(0, 0, 0, $yesterday[0], $yesterday[1], $yesterday[2]);
$endDateYesterday = TIME; //mktime(23, 59, 59, $yesterday[0], $yesterday[1], $yesterday[2]);

// Stats Visits
$sql = "SELECT ip, count(*) AS total".
	" FROM statistics".
	" WHERE (browser IS NULL".
	" AND os IS NULL) = 0".
	" AND dateVisit BETWEEN ? AND ?".
	" GROUP BY ip".
	" ORDER BY ip";
$visits = new stdClass();
$visits->externe = 0;
$visits->interne = 0;
$visits->cri = 0;
$stats = $db->prepare($sql);
if($stats->execute(array($beginDateYesterday, $endDateYesterday))){
	$total = 0;
	while($stat = $stats->fetch()){
		switch($stat[0]){
			case 0: $visits->externe = $stat[1];break;
			case 1: $visits->interne = $stat[1];break;
			default: $visits->cri = $stat[1];break;
		}
		$total += $stat[1];
	}
	$visits->count = $total;
}

// Stats Browsers
$sql = "SELECT browser, count(*) AS total".
	" FROM statistics".
	" WHERE browser NOT LIKE 'other'".
	" AND dateVisit BETWEEN ? AND ?".
	" GROUP BY browser".
	" ORDER BY total DESC";
$browsers = array();
$stats = $db->prepare($sql);
if($stats->execute(array($beginDateYesterday, $endDateYesterday))){
	while($stat = $stats->fetch(PDO::FETCH_OBJ)){
		$browsers[] = $stat;
	}
}

// Stats Operating System
$sql = "SELECT os, count(*) AS total".
	" FROM statistics".
	" WHERE os NOT LIKE 'other'".
	" AND dateVisit BETWEEN ? AND ?".
	" GROUP BY os".
	" ORDER BY total DESC";
$os = array();
$stats = $db->prepare($sql);
if($stats->execute(array($beginDateYesterday, $endDateYesterday))){
	while($stat = $stats->fetch(PDO::FETCH_OBJ)){
		$os[] = $stat;
	}
}

// Stats Bots
$sql = "SELECT userAgent, count(*) AS total".
	" FROM statistics".
	" WHERE browser LIKE 'other'".
	" AND dateVisit BETWEEN ? AND ?".
	" GROUP BY userAgent".
	" ORDER BY total DESC";
$bots = array();
$googlebot = new stdClass();
$googlebot->userAgent = 'Googlebot';
$googlebot->total = 0;
$total = 0;

$stats = $db->prepare($sql);
if($stats->execute(array($beginDateYesterday, $endDateYesterday))){
	while($stat = $stats->fetch(PDO::FETCH_OBJ)){
		if(strstr($stat->userAgent, 'Googlebot') !== false){
			$googlebot = $stat;
		}else{
			if(!empty($stat->userAgent)){
				$bots[] = $stat;
			}
			$total += $stat->total;
		}
	}
}

$visits->bots = $total+$googlebot->total;
$smarty->assign('visits', $visits);
$smarty->assign('browsers', $browsers);
$smarty->assign('os', $os);
$smarty->assign('bots', $bots);
$smarty->assign('googlebot', $googlebot);

//
// SUMMARY OF EVENTS
//
require BASE.'/classes/isou/isou_service.class.php';
require BASE.'/classes/isou/isou_event.class.php';

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

		$service->setEvents($service->getAllEvents($CFG['tolerance'], 10, $beginDateYesterday, $endDateYesterday));

		if($service->hasEvents() === TRUE){
			if($categoryName !== $service->getCategoryName()){
				$categoryName = $service->getCategoryName();
				$categories[] = new stdClass();
				$categories[count($categories)-1]->name = $service->getCategoryName();
				$categories[count($categories)-1]->services = array();
			}

			$service->stripName = strip_accents($service->getNameForUsers());

			$service->total = 0;
			foreach($service->getEvents() as $event){
				$endDate = $event->getEndDate();
				if($endDate === NULL){
					$endDate = TIME;
				}
				$service->total += $endDate - $event->getBeginDate();
			}
			$service->total = secondstohuman($service->total);
			$categories[count($categories)-1]->services[] = $service;
			$i++;
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
			$db->query($sql);

			if($nameForUsers = $db->query($sql)){
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
$services = $db->query($sql);

$i = 0;
$nagiosServices = array();
while($service = $services->fetchObject()){
	if(!in_array(stripslashes($service->name), $arrayNagios)){
		$nagiosServices[] = $service->name;
	}
	$i++;
}

$smarty->assign('nagiosServices', $nagiosServices);

$subject = 'Rapport ISOU du '.strftime('%A %e %B', $beginDateYesterday);
$content = $smarty->fetch('mail_cron_daily_text.tpl');
$header = "MIME-Version: 1.0\r\n Content-type: text/plain; charset=UTF-8\r\n";

foreach($CFG['admin_mails'] as $mail){
	if(filter_var($mail, FILTER_VALIDATE_EMAIL) !== FALSE){
		mail($mail, utf8_decode($subject), $content, $header);
	}
}

?>
