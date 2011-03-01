<?php

// add_log(BASE.'/log/fisou.log', 'FISOU', 'JSON', getIpAddr());

// find filter on rss url
if(isset($_GET['key'])){
	$maxKey = pow(2,100);
	$key = hexdec($_GET['key']);
	$rssKey = '';
	$i=100;
	while($key != 0){//;$i>0;$i--){
		if($key >= $maxKey){
			$rssKey .= $i.',';
			$key = $key-$maxKey;
		}
		$maxKey /= 2;
		$i--;
	}
	$rssKey = ' AND S.rssKey IN ('.substr($rssKey, 0, -1).')';
}else{
	$rssKey = ' AND S.rssKey IS NOT NULL';
}

try{
	$db = new PDO(DB_PATH, '', '');
}catch(PDOException $e){
	add_log(LOG_FILE, 'ISOU', 'ERROR_DB', $e->getMessage());
	exit();
}

$sql = "SELECT S.nameForUsers as name, S.state as state, E.beginDate as date, ED.description".
	" FROM services S, events_description ED, events E, events_isou EI".
	" WHERE S.idService = EI.idService".
	" AND E.idEvent = EI.idEvent".
	" AND ED.idEventDescription = EI.idEventDescription".
	" AND (E.endDate IS NULL".
	" OR E.beginDate > ".TIME.")".
	" AND EI.isScheduled = 0".
	" AND S.state BETWEEN 1 AND 3".
	" AND S.enable = 1".
	" AND S.visible = 1".
	$rssKey.
	" ORDER BY UPPER(S.nameForUsers), S.state";

$services = array();
$json = array("fisou" => $services);

if($events = $db->query($sql)){
	while($service = $events->fetch(PDO::FETCH_ASSOC)){
		$service['description'] = explode("\n", trim($service['description']));
		if(count($service['description']) === 1 && empty($service['description'][0])){
			$service['description'] = array();
		}
		$services[] = $service;
	}
	$json['fisou']['services'] = $services;
}

file_put_contents($pwd.'/isou.json', json_encode($json));

?>
