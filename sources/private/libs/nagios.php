<?php

function get_updated_nagios_statusdat_services(){
	$updated_services = array();

	$statusdat = get_nagios_services_from_statusdat_sorted_by_name();
	$services = get_services(SERVICETYPE_NAGIOS_STATUSDAT);
	foreach($services as $service){
		if(isset($statusdat[$service->name]) && $service->state !== $statusdat[$service->name]->current_state){
			// si le statut du service Nagios dans le fichier status.dat et dans la base de données ne sont pas identiques...
			$service->change_state($statusdat[$service->name]->current_state);
			$updated_services[] = $service;
		} elseif($service->state !== STATE_OK ) {
			$service->change_state(STATE_OK);
		}
	}

	return $updated_services;
}

function get_nagios_services_from_statusdat_sorted_by_name(){
	global $CFG;

	if(is_readable($CFG['nagios_statusdat_path'])){
		$handle = @fopen($CFG['nagios_statusdat_path'], 'r');
		if($handle){
			$services = array();

			while(!feof($handle)){
				$line = trim(fgets($handle, 4096));
				if(preg_match('#hoststatus \{|servicestatus \{#', $line)){
					$service = new stdClass();
					$service->tag = substr($line,0,-2);
					$service->name = '';
					$service->description = '';
					$service->check_command = '';
					$service->current_state = 0;
					$service->problem_has_been_acknowledged = 0;
					while(!feof($handle)){
						$line = trim(fgets($handle, 4096));
						if($line === '}'){
							break;
						}

						if(preg_match('#host_name=|service_description=|check_command=|current_state=|problem_has_been_acknowledged=|is_flapping=#',$line)){
							$split = explode('=',$line);
							switch($split[0]){
								case 'host_name':
									$service->name = $split[1];
									break;
								case 'service_description':
									$service->description = $split[1];
									break;
								case 'check_command':
									$service->check_command = $split[1];
									break;
								case 'current_state':
									$service->current_state = $split[1];
									break;
								case 'problem_has_been_acknowledged':
									$service->problem_has_been_acknowledged = $split[1];
									break;
								case 'is_flapping':
									$service->is_flapping = $split[1];
									break;
							}
						}
					}

					// passe le service en rouge si il est en "flapping"
					if($service->is_flapping === '1'){
						$service->current_state = STATE_WARNING;
					}

					// passe le service en vert si le problème est connu
					// why ? remove it...
					/*
					if($service->problem_has_been_acknowledged === '1'){
						$service->problem_has_been_acknowledged = '0';
						$service->current_state = '0';
					}*/

					if($service->tag == 'servicestatus'){
						$service->type = 'service';
						$service->name = $service->description.'@'.$service->name;
					}else{
						$service->type = 'host';
					}

					$services[$service->name] = $service;
				}
			}
			fclose($handle);

			return $services;
		}
	}

	return NULL;
}

function get_nagios_services_from_statusdat(){
	global $CFG;

	if(is_readable($CFG['nagios_statusdat_path'])){
		$handle = @fopen($CFG['nagios_statusdat_path'], 'r');
		if($handle){
			$registred_services = array();
			foreach(get_services(SERVICETYPE_NAGIOS_STATUSDAT) as $service){
				$registred_services[$service->name] = $service->name;
			}

			$services = array('Services' => array(), 'Hôtes' => array());
			while(!feof($handle)){
				$line = trim(fgets($handle, 4096));
				if(preg_match('#hoststatus \{|servicestatus \{#', $line)){
					$tag = substr($line,0,-2);
					$name = '';
					$description = '';

					while(!feof($handle)){
						$line = trim(fgets($handle, 4096));
						if($line === '}'){
							break;
						}

						if(preg_match('#host_name=|service_description=#', $line)){
							$split = explode('=', $line);
							if($split[0] === 'host_name'){
								$name = $split[1];
							}else{
								$description = $split[1];
							}
						}
					}

					if($tag == 'servicestatus'){
						$tag = 'Services';
						$name = $description.'@'.$name;
					}else{
						$tag = 'Hôtes';
					}

					if(!isset($registred_services[$name])){
						$services[$tag][$name] = $name;
					}
				}
			}
			fclose($handle);

			if(count($services['Services']) === 0){
				unset($services['Services']);
			}

			if(count($services['Hôtes']) === 0){
				unset($services['Hôtes']);
			}

			return $services;
		}
	}

	return NULL;
}

?>
