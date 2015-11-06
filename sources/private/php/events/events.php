<?php

	$TITLE = NAME.' - Administration des Evènements';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery.dynDateTime.min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery.dynDateTime-fr.min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_events.js');

	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/events.css');
	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/private_events_calendar.css', 'all');

	$p=0;
	$date = getdate();

	define('TIMESTAMP_OF_LAST_CALENDAR_DAY', ((mktime(0,0,0)-(6+$date["wday"])*24*60*60)+35*24*60*60));
	define('TIMESTAMP_OF_72H_BEFORE_TODAY', mktime(0,0,0)-3*24*60*60);

	if(count($_POST) > 0){
		require PRIVATE_PATH.'/php/events/formsprocess.php';
	}

	require PRIVATE_PATH.'/php/events/elementsforms.php';

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
				require PRIVATE_PATH.'/php/events/unscheduled.php';
				$smarty->assign('unscheduled', $unscheduled);
				break;
			case 'reguliers' :
				$_GET['type'] = 2;
			   	require PRIVATE_PATH.'/php/events/regular.php';
				$smarty->assign('regular', $regular);
				break;
			case 'fermes' :
				$_GET['type'] = 3;
				require PRIVATE_PATH.'/php/events/closed.php';
				$smarty->assign('closed', $closed);
				break;
			case 'messages' :
				$_GET['type'] = 4;
				require PRIVATE_PATH.'/php/events/messages.php';
				$smarty->assign('messages', $messages);
				break;
			default : 
				require PRIVATE_PATH.'/php/events/scheduled.php';
				$smarty->assign('scheduled', $scheduled);
				break;
		}
	}else{
		require PRIVATE_PATH.'/php/events/scheduled.php';
		$smarty->assign('scheduled', $scheduled);
	}

	require PRIVATE_PATH.'/php/events/forced.php';
	$smarty->assign('forcedservices', $forcedservices);

	$smarty->assign('optionNameForUsers', $optionNameForUsers);
	if(isset($currentEdit)){
		$smarty->assign('currentEdit', $currentEdit);
	}

	$template = 'events/view.tpl';

?>


