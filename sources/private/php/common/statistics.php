<?php

$sql = "SELECT session_id".
		" FROM statistics".
		" WHERE session_id=?".
		" AND dateVisit < ?";
$stats = $db->prepare($sql);
$stats->execute(array(session_id(), TIME+60*60));
$stats = $stats->fetch();

if(!$stats[0]){
	require BASE.'/common/statistics.php';

	$user_agent = $_SERVER["HTTP_USER_AGENT"];
	$operating_system = getOperatingSystem($user_agent);
	$internet_browser = getInternetBrowser($user_agent);
	$ip_addr = getIpAddr();

	$ip = 0; // ip externe
	foreach($CFG['ip_service'] as $ip_service){
		if(in_range($ip_addr, $ip_service)){
			$ip = 2; // ip du service
			continue;
		}
	}

	if($ip === 0){
		foreach($CFG['ip_local'] as $ip_local){
			if(in_range($ip_addr, $ip_local)){
				$ip = 1; // ip du réseau local
				continue;
			}
		}
	}

	if($operating_system === 'other' || $internet_browser === 'other'){
		$params = array(session_id(), $operating_system, $internet_browser, $ip, $_SERVER['HTTP_USER_AGENT']);
	}else{
		$params = array(session_id(), $operating_system, $internet_browser, $ip, NULL, TIME);
	}

	$sql = "INSERT INTO statistics(session_id, os, browser, ip, userAgent, dateVisit) VALUES(?,?,?,?,?,?)";
	$query = $db->prepare($sql);
	$query->execute($params);
}

?>
