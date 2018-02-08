<?php

use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

function get_updated_shinken_thruk_services(){
	global $CFG, $LOGGER;

	$updated_services = array();

	$statusdat = get_shinken_services_from_thruk($CFG['shinken_thruk_path'], $CFG['shinken_thruk_username'], $CFG['shinken_thruk_password']);
	$services = get_services(array('enable' => true, 'type' => Service::TYPE_SHINKEN_THRUK));

	foreach($services as $service){
		if (isset($statusdat[$service->name])) {
			$LOGGER->addInfo('Check '.$service->name.' state: '.$service->state.' (current) | '.$statusdat[$service->name]['current_state'].' (new)');
			if ($service->state != $statusdat[$service->name]['current_state']) {
				$LOGGER->addInfo('Mise à jour de l\'état du service '.$service->name.' (état #'.$service->state.')');

				// si le statut du service dans Shinken n'est pas identique à celui renseigné dans la base de données...
				$service->change_state($statusdat[$service->name]['current_state']);
				$updated_services[] = $service;
			}
		} else {
			// le service n'est plus présent dans Shinken.
			$service->disable();
			$LOGGER->addInfo('Le service '.$service->name.' n\'est plus présent dans Shinken.');
		}

		$service->change_state(Isou\State::CRITICAL);
		$updated_services[] = $service;
	}

	return $updated_services;
}


function get_shinken_services_from_thruk($url, $username='', $password=''){

	$cache_file = CACHE_PATH.'/services/shinken.json';
	if (is_file($cache_file)) {
		try {
			$now = new DateTime();

			$last_cache_update = new DateTime(date("F d Y H:i:s.", filectime($cache_file)));
			// query shinken only if cache older than 5 min
			if($now->sub(new DateInterval('PT5M')) < $last_cache_update){
				// use cache
				$services = json_decode(file_get_contents($cache_file), true);

				if (!empty($services)) {
					return $services;
				}
			}

		} catch (Exception $exception) {
		}
	}

	$url .= '?host=all&view_mode=json&columns=host_name,description,state,acknowledged,is_flapping';

	$params = array(
		'http' => array(
			'method' => 'GET',
			'header' => 'Authorization: Basic '.base64_encode($username.':'.$password)
			)
		);

	$ctx = stream_context_create($params);

	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
		throw new Exception("Problem with $url, $php_errormsg");
	}

	$response = @stream_get_contents($fp);

	if ($response === false) {
		throw new Exception("Problem reading data from $url, $php_errormsg");
	}

	$response = json_decode($response);

	$services = array();

	foreach($response as $element){
		if(isset($element->description)){
			$service = new stdClass();
			$service->name = $element->description.'@'.$element->host_name;
			$service->current_state = $element->state;
			$service->problem_has_been_acknowledged = $element->acknowledged;
			$service->is_flapping = $element->is_flapping;

			$services[$service->name] = $service;
		}
	}

	uasort($services, function($a, $b){
			if ($a->name == $b->name) {
				return 0;
			}

			return ($a->name < $b->name) ? -1 : 1;
		}
	);

	// set cache
	file_put_contents($cache_file, json_encode($services));

	return $services;
}

?>
