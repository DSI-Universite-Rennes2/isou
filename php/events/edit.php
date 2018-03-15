<?php

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\EventDescription;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

if(isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])){
	$event = get_event($PAGE_NAME[2]);
}else{
	$event = FALSE;
}

if($event === FALSE){
	$event = new Event();
}

if ($event->idservice !== 0) {
	$service = get_service(array('id' => $event->idservice, 'plugin' => PLUGIN_ISOU));
	$event->locked = $service->locked;
} else {
	$event->locked = 0;
}

$options_states = State::$STATES;

$options_periods = Event::$PERIODS;

$options_services = get_isou_services_sorted_by_idtype();

$options_types = Event::$TYPES;

$options_yesno = array('1' => 'Oui', '0' => 'Non');

if(isset($_POST['type'], $_POST['service'], $_POST['startdate'], $_POST['starttime'], $_POST['enddate'], $_POST['endtime'], $_POST['period'], $_POST['description'])){
	$_POST['errors'] = array();

	try{
		$event->set_type($_POST['type']);
	}catch(Exception $exception){
		$_POST['errors'][] = $exception->getMessage();
	}

	try{
		$event->set_service($_POST['service'], $options_services);
	}catch(Exception $exception){
		$_POST['errors'][] = $exception->getMessage();
	}

	try{
		$event->set_state($_POST['state'], $options_states);
	}catch(Exception $exception){
		$_POST['errors'][] = $exception->getMessage();
	}

	try{
		$event->set_startdate($_POST['startdate'], $_POST['starttime']);
	}catch(Exception $exception){
		$_POST['errors'][] = $exception->getMessage();
	}

	try{
		$event->set_enddate($_POST['enddate'], $_POST['endtime']);
	}catch(Exception $exception){
		$_POST['errors'][] = $exception->getMessage();
	}

	try{
		$event->set_period($_POST['period']);
	}catch(Exception $exception){
		$_POST['errors'][] = $exception->getMessage();
	}

	$event_description = get_event_description_by_content($_POST['description']);
	if($event_description === FALSE){
		$event_description = new EventDescription();
		$event_description->description = $_POST['description'];
		$event_description->autogen = 0;
	}

	if(!isset($_POST['errors'][0])){
		$DB->beginTransaction();

		try{
			if($event_description->id === 0){
				$event_description->save();
				$event->ideventdescription = $event_description->id;
			}

			$event->save();

			if (isset($_POST['locked'])) {
				$service = get_service(array('id' => $event->idservice, 'plugin' => PLUGIN_ISOU));

				if ($_POST['locked'] === '1') {
					$service->lock($event->state);
				} else {
					$service->unlock();
				}
			}

			$DB->commit();
		}catch(Exception $exception){
			$DB->rollBack();
			$_POST['errors'] = array($exception->getMessage());
		}

		if(!isset($_POST['errors'][0])){
			$_SESSION['messages'] = array('successes' => 'L\'évènement a été enregistré.');

			if($event->type === Event::TYPE_UNSCHEDULED){
				header('Location: '.URL.'/index.php/evenements/imprevus');
			}else{
				header('Location: '.URL.'/index.php/evenements/prevus');
			}
			exit(0);
		}
	}
}

$smarty->assign('options_states', $options_states);
$smarty->assign('options_periods', $options_periods);
$smarty->assign('options_services', $options_services);
$smarty->assign('options_types', $options_types);
$smarty->assign('options_yesno', $options_yesno);

$smarty->assign('event', $event);

$TEMPLATE = 'events/edit.tpl';
