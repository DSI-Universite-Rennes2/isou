<?php

$sql = "SELECT session_id
	FROM statistics
	WHERE session_id='".session_id()."'
	AND dateVisit < ".(TIME+60*60);
$stats = $db->query($sql);
$stats = $stats->fetch();

if(!$stats[0]){
	require BASE.'/classes/isou/statistics.function.php';

	$user_agent = $_SERVER["HTTP_USER_AGENT"];
	$operating_system = getOperatingSystem($user_agent);
	$internet_browser = getInternetBrowser($user_agent);
	$ip_addr = getIpAddr();

	$ip = 0; // ip externe
	foreach($IP_CRI as $ip_cri){
		if(in_range($ip_addr, $ip_cri)){
			$ip = 2; // ip cri
			continue;
		}
	}

	if($ip === 0){
		foreach($IP_INTERNE as $ip_interne){
			if(in_range($ip_addr, $ip_interne)){
				$ip = 1; // ip interne
				continue;
			}
		}
	}

	if($operating_system === 'other' || $internet_browser === 'other'){
		$sql="INSERT INTO statistics VALUES('".session_id()."', '".$operating_system."', '".$internet_browser."', '".$ip."', '".$_SERVER["HTTP_USER_AGENT"]."', ".TIME.")";
	}else{
		$sql="INSERT INTO statistics VALUES('".session_id()."', '".$operating_system."', '".$internet_browser."', '".$ip."', NULL, ".TIME.")";
	}

	$db->exec($sql);
}

?>
