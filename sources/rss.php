<?php

header("content-type: application/xml");

// chemin d'accès du site
$pwd = dirname(__FILE__);

define('MAXFEED',100);

require $pwd.'/functions.php';
require $pwd.'/config.php';
require BASE.'/classes/isou/isou_event.class.php';
require BASE.'/classes/smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = BASE.'/html/';
$smarty->compile_dir = BASE.'/classes/smarty/compile/';

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


// interruption de services
$sql = 'SELECT E.idEvent, E.beginDate, E.endDate, ED.description, EI.isScheduled, S.nameForUsers, C.name'.
	' FROM events E, events_isou EI, events_description ED, services S, categories C'.
	' WHERE S.idService = EI.idService'.
	' AND E.idEvent = EI.idEvent'.
	' AND ED.idEventDescription = EI.idEventDescription'.
	' AND C.idCategory = S.idCategory'.
	' AND S.visible = 1'.
	' AND S.enable = 1'.
	$rssKey.
	' AND EI.isScheduled < 2'.
	' AND E.beginDate BETWEEN '.($now-(30*24*60*60)).' AND '.(TIME+10*60).
	' AND (E.endDate IS NULL OR E.endDate-E.beginDate > (10*60))'.
	' LIMIT 0, 200';

if($event_records = $db->query($sql)){
	while($event = $event_records->fetch()){
		$record[count($record)] = array('b',new IsouEvent($event[0],$event[1],$event[2],NULL,$event[3],$event[5],NULL,$event[4]),$event[6]);
	}
}

// reprise de services
$sql = 'SELECT E.idEvent, E.endDate, E.beginDate, ED.description, EI.isScheduled, S.nameForUsers, C.name'.
	' FROM events E, events_isou EI, events_description ED, services S, categories C'.
	' WHERE S.idService = EI.idService '.
	' AND E.idEvent = EI.idEvent'.
	' AND ED.idEventDescription = EI.idEventDescription'.
	' AND C.idCategory = S.idCategory'.
	' AND S.visible = 1'.
	' AND S.enable = 1'.
	$rssKey.
	' AND EI.isScheduled < 2'.
	' AND E.endDate BETWEEN '.($now-(30*24*60*60)).' AND '.(TIME+10*60).
	' AND E.endDate IS NOT NULL'.
	' AND E.endDate-E.beginDate > (10*60)'.
	' LIMIT 0, 200';

if($event_records = $db->query($sql)){
	while($event = $event_records->fetch()){
		$record[count($record)] = array('e',new IsouEvent($event[0],$event[1],$event[2],NULL,$event[3],$event[5],NULL,$event[4]),$event[6]);
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

	$beginDate = $record[$i][1]->getBeginDate();
	$endDate = $record[$i][1]->getEndDate();

	$title = '';
	$link = '';
	$pubDate = '';
	$description = '';

	if($record[$i][0] == 'b'){
		$description = $record[$i][1]->Message(true, true, false);
		$description = '';
		$pubDate =  gmdate("D, d M Y H:i:s", $beginDate);
		if($record[$i][1]->getScheduled() == 1){
			$title = 'Interruption : '.$record[$i][1]->getServiceName();
			if(!is_null($record[$i][1]->getDescription())){
				$description = $record[$i][1]->getDescription();
			}
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

	if($description == ''){
		$description = 'n/a';
	}

	(empty($endDate))?$endDate = 'Indéterminée':$endDate=strftime('%A %e %B %R',$endDate);
	$description = 'Catégorie : '.$record[$i][2].'<br>'.
					'Date de début : '.strftime('%A %e %B %R',$beginDate).'<br/>'.
					'Date de fin : '.$endDate.'<br/><br/>Description :<br/>'.$description;

	$items[count($items)] = array($title,$link,$pubDate,'<![CDATA['.$description.']]>',$link);

}

$smarty->assign('lastBuildDate', gmdate("D, d M Y H:i:s",TIME));
$smarty->assign('RSS_URL', RSS_URL);
$smarty->assign('items', $items);

$smarty->display('public_rss.tpl');


?>
