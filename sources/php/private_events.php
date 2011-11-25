<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/events.css" media="screen">'.
			'<link rel="stylesheet" type="text/css" media="all" href="'.URL.'/css/private_events_calendar.css"  />';

	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-min.js"></script>'.
				'<script type="text/javascript" src="'.URL.'/js/jquery.dynDateTime.min.js"></script>'.
				'<script type="text/javascript" src="'.URL.'/js/jquery.dynDateTime-fr.min.js"></script>'.
				'<script type="text/javascript" src="'.URL.'/js/jquery_events.js"></script>';

	$title = NAME.' - Administration des Evènements';

	$p=0;
	$date = getdate();

	define('TIMESTAMP_OF_LAST_CALENDAR_DAY', ((mktime(0,0,0)-(6+$date["wday"])*24*60*60)+35*24*60*60));
	define('TIMESTAMP_OF_72H_BEFORE_TODAY', mktime(0,0,0)-3*24*60*60);

	if(count($_POST) > 0){
		require BASE.'/php/private_events.formsprocess.php';
	}

	require BASE.'/php/private_events.elementsforms.php';

	/* * * * * * * * * * *
	 * Données formulaire
	 * * * * * * * * * * */

	if(isset($_POST['scheduled']) && $_POST['scheduled']==2){
		$scheduledRadio2 = 'checked="checked" ';
		$scheduledRadio1 = '';
	}else{
		$scheduledRadio1 = 'checked="checked" ';
		$scheduledRadio2 = '';
	}

	(isset($_POST['name']))?$name=$_POST['name']:$name=0;

	(isset($_POST['beginDate']))?$beginDate=$_POST['beginDate']:$beginDate=strftime('%d/%m/%Y %H:%M',TIME);

	(isset($_POST['endDate']))?$endDate=$_POST['endDate']:$endDate=strftime('%d/%m/%Y %H:%M',TIME);

	if(isset($_POST['period']) && $_POST['period']=='weekly'){
		$periodRadio2 = 'checked="checked" ';
		$periodRadio1 = '';
	}else{
		$periodRadio1 = 'checked="checked" ';
		$periodRadio2 = '';
	}

	(isset($_POST['description']))?$description=$_POST['description']:$description='';

	$_GET['type'] = 1;
	if(isset($PAGE_NAME[1])){
		switch($PAGE_NAME[1]){
			case 'nonprevus' : 
				$_GET['type'] = 0;
				require BASE.'/php/private_events_unscheduled.php';
				$smarty->assign('unscheduled', $unscheduled);
				break;
			case 'reguliers' :
				$_GET['type'] = 2;
			   	require BASE.'/php/private_events_regular.php';
				$smarty->assign('regular', $regular);
				break;
			case 'fermes' :
				$_GET['type'] = 3;
				require BASE.'/php/private_events_closed.php';
				$smarty->assign('closed', $closed);
				break;
			case 'messages' :
				$_GET['type'] = 4;
				require BASE.'/php/private_events_messages.php';
				$smarty->assign('messages', $messages);
				break;
			default : 
				require BASE.'/php/private_events_scheduled.php';
				$smarty->assign('scheduled', $scheduled);
				break;
		}
	}else{
		require BASE.'/php/private_events_scheduled.php';
		$smarty->assign('scheduled', $scheduled);
	}

	require BASE.'/php/private_events_forced.php';
	$smarty->assign('forcedservices', $forcedservices);

	$smarty->assign('optionNameForUsers', $optionNameForUsers);
	if(isset($currentEdit)){
		$smarty->assign('currentEdit', $currentEdit);
	}

?>


