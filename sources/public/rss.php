<?php

header("content-type: application/xml");

define('MAXFEED',100);

require __DIR__.'/config.php';
require PRIVATE_PATH.'/classes/isou/isou_event.class.php';
require PRIVATE_PATH.'/classes/smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = PRIVATE_PATH.'/html/';
$smarty->compile_dir = PRIVATE_PATH.'/classes/smarty/compile/';

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

$now = mktime(0,0,0);
$record = array();
$items = array();

try {
	$db = new PDO(DB_PATH, '', '');
} catch (PDOException $e) {
	header("HTTP/1.0 503 Service Unavailable");

	// close pdo connection
	$db = null;

	exit(0);
}

$sql = "SELECT key, value FROM configuration";
$CFG = array();
if($query = $db->query($sql)){
	while($config = $query->fetch(PDO::FETCH_OBJ)){
		if(in_array($config->key, array('ip_local', 'ip_service', 'admin_users', 'admin_mails'))){
			 $CFG[$config->key] = json_decode($config->value);
		}else{
			$CFG[$config->key] = $config->value;
		}
	}
}


// interruption de services
$firstDate = strftime('%Y-%m-%dT%H:%M', $now-(30*24*60*60));
$lastDate = strftime('%Y-%m-%dT%H:%M', TIME+10*60);

$sql = 'SELECT E.idEvent, strftime(\'%s\',E.beginDate) AS beginDate, strftime(\'%s\',E.endDate) AS endDate, ED.description, EI.isScheduled, S.nameForUsers, C.name'.
	' FROM events E, events_isou EI, events_description ED, services S, categories C'.
	' WHERE S.idService = EI.idService'.
	' AND E.idEvent = EI.idEvent'.
	' AND ED.idEventDescription = EI.idEventDescription'.
	' AND C.idCategory = S.idCategory'.
	' AND S.visible = 1'.
	' AND S.enable = 1'.
	$rssKey.
	' AND EI.isScheduled < 2'.
	' AND E.beginDate BETWEEN ? AND ?'.
	' AND (E.endDate IS NULL OR strftime(\'%s\',E.endDate)-strftime(\'%s\',E.beginDate) > '.$CFG['tolerance'].')'.
	' LIMIT 0, 200';
$event_records = $db->prepare($sql);
if($event_records->execute(array($firstDate, $lastDate))){
	while($event = $event_records->fetch()){
		$record[count($record)] = array('b', new IsouEvent($event[0],$event[1],$event[2],NULL,$event[5], 0, $event[4], NULL, NULL, $event[3]),$event[6]);
	}
}

// reprise de services
$sql = 'SELECT E.idEvent, strftime(\'%s\',E.endDate) AS endDate, strftime(\'%s\',E.beginDate) AS beginDate, ED.description, EI.isScheduled, S.nameForUsers, C.name'.
	' FROM events E, events_isou EI, events_description ED, services S, categories C'.
	' WHERE S.idService = EI.idService '.
	' AND E.idEvent = EI.idEvent'.
	' AND ED.idEventDescription = EI.idEventDescription'.
	' AND C.idCategory = S.idCategory'.
	' AND S.visible = 1'.
	' AND S.enable = 1'.
	$rssKey.
	' AND EI.isScheduled < 2'.
	' AND E.endDate BETWEEN ? AND ?'.
	' AND E.endDate IS NOT NULL'.
	' AND strftime(\'%s\',E.endDate)-strftime(\'%s\',E.beginDate) > '.$CFG['tolerance'].
	' LIMIT 0, 200';
$event_records = $db->prepare($sql);
if($event_records->execute(array($firstDate, $lastDate))){
	while($event = $event_records->fetch()){
		$record[count($record)] = array('e',new IsouEvent($event[0],$event[1],$event[2],NULL,$event[5], 0, $event[4], NULL, NULL, $event[3]),$event[6]);
	}
}

// tri des evenements
$i=0;
while($i < count($record)){
	$min = $record[$i][1]->getBeginDate();
	$idMin = $i;

	for($j=$i+1;$j<count($record);$j++){
		if($min > $record[$j][1]->getBeginDate()){
			$min = $record[$j][1]->getBeginDate();
			$idMin = $j;
		}
	}

	$temp = $record[$i];
	$record[$i] = $record[$idMin];
	$record[$idMin] = $temp;

	$i++;
}

// extraction des informations et ajout au contenu
if(count($record) > MAXFEED){
	$maxFeed = count($record)-MAXFEED;
}else{
	$maxFeed = 0;
}

for($i=count($record)-1;$i>=$maxFeed;$i--){

	$beginDate = strtotime($record[$i][1]->getBeginDate());
	$endDate = strtotime($record[$i][1]->getEndDate());

	$title = '';
	$link = '';
	$pubDate = '';

	if($record[$i][0] == 'b'){
		//$description = $record[$i][1]->Message(true, true, false);
		$pubDate =  gmdate("D, d M Y H:i:s", $beginDate);
		$description = $record[$i][1]->getDescription();
		if($record[$i][1]->getScheduled() == 1){
			$title = 'Interruption : '.$record[$i][1]->getServiceName();
		}else{
			$title = 'Interruption non prévue : '.$record[$i][1]->getServiceName();
			$endDate = '';
		}
		$link = ISOU_URL.'?feed='.$record[$i][1]->getId().'&amp;type=I#'.rawurlencode($record[$i][1]->getServiceName());
	}

	if($record[$i][0] == 'e'){
		$record[$i][1]->setBeginDate($endDate);
		$record[$i][1]->setEndDate($beginDate);
		$beginDate = $record[$i][1]->getBeginDate();
		$endDate = $record[$i][1]->getEndDate();
		$description = 'Remise en route du service '.$record[$i][1]->getServiceName();

		// in this case, $beginDate is an EndDate value
		$pubDate =  gmdate("D, d M Y H:i:s", $endDate);
		$title = 'Remise en route : '.$record[$i][1]->getServiceName();
		$link = ISOU_URL.'?feed='.$record[$i][1]->getId().'&amp;type=R#'.rawurlencode($record[$i][1]->getServiceName());
	}

	if($description === NULL){
		$description = 'n/a';
	}

	(empty($endDate))?$endDate = 'Indéterminée':$endDate=strftime('%A %e %B %R',$endDate);
	$description = 'Catégorie : '.$record[$i][2].'<br>'.
					'Date de début : '.strftime('%A %e %B %R',$beginDate).'<br/>'.
					'Date de fin : '.$endDate.'<br/><br/>Description :<br/>'.$description;

	$items[count($items)] = array($title, $link, $pubDate, $description, $link);

}

$smarty->assign('lastBuildDate', gmdate("D, d M Y H:i:s",TIME));
$smarty->assign('RSS_URL', RSS_URL);
$smarty->assign('items', $items);

$smarty->display('public_rss.tpl');


?>
