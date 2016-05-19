<?php

if(isset($PAGE_NAME[3]) && ctype_digit($PAGE_NAME[3])){
	$service = get_service($PAGE_NAME[3], $backend->type);
}else{
	$service = FALSE;
}

if($service === FALSE){
	$service = new UniversiteRennes2\Isou\Service();
	$service->idtype = $backend->type;
}

// traitement de la recherche
if(isset($_POST['search'])){

	if($backend->type === UniversiteRennes2\Isou\Service::TYPE_SHINKEN_THRUK){
		require_once PRIVATE_PATH.'/libs/shinken.php';
		$backend_services = get_shinken_services_from_thruk($CFG['shinken_thruk_path'], $CFG['shinken_thruk_username'], $CFG['shinken_thruk_password']);
	}elseif($backend->type === UniversiteRennes2\Isou\Service::TYPE_NAGIOS_STATUSDAT){
		require_once PRIVATE_PATH.'/libs/nagios.php';
		$backend_services = get_nagios_services_from_statusdat_sorted_by_name();
	}else{
		die('TODO');
	}

	if($backend_services === NULL){
		$_SESSION['cached_search_services'] = null;
	}else{
		$services = array();
		$local_backend_services = get_services_sorted_by_id($backend->type);

		$terms = explode(' ', $_POST['search']);
		foreach($terms as $i => $term){
			$trim = trim($term);
			if(empty($term)){
				unset($terms[$i]);
			}else{
				$terms[$i] = $trim;
			}
		}

		if(count($terms) > 0){
			foreach($backend_services as $backend_service){
				$found = true;
				foreach($terms as $term){
					$found &= (stripos($backend_service->name, $term) !== false);
					if($found === 0){
						break;
					}
				}

				if($found === 1){
					$backend_service->disabled = in_array($backend_service->name, $local_backend_services);

					$services[] = $backend_service;
				}
			}
		}

		if(count($services) > 30){
			$services = false;
		}

		$_SESSION['cached_search_term'] = $_POST['search'];

		if(!isset($_SESSION['cached_search_services'])){
			$_SESSION['cached_search_services'] = array();
		}
		$_SESSION['cached_search_services'][$_GET['service']] = $services;

		if(!isset($_SESSION['cached_backend_services'])){
			$_SESSION['cached_backend_services'] = array();
		}
		$_SESSION['cached_backend_services'][$_GET['service']] = $local_backend_services;
	}
}else if(isset($_POST['services']) || isset($_POST['service']) ){
	if(isset($_POST['services']) && is_array($_POST['services']) && $service->id === 0){
		// insert
		foreach($_POST['services'] as $name){
			if(in_array($name, $_SESSION['cached_backend_services'][$_GET['service']])){
				continue;
			}

			$service->name = $name;
			$service->check_data();
			if(!isset($_POST['errors'][0])){
				$_POST = array_merge($_POST, $service->save());

				foreach($_SESSION['cached_search_services'][$_GET['service']] as $index => $cached_search_service){
					if($service->name === $cached_search_service->name){
						$_SESSION['cached_search_services'][$_GET['service']][$index]->disabled = true;
						break;
					}
				}
			}

			$service = new UniversiteRennes2\Isou\Service();
			$service->idtype = $backend->type;
		}
	}elseif(isset($_POST['service']) && is_string($_POST['service']) && $service->id !== 0){
		// update
		if(in_array($_POST['service'], $_SESSION['cached_backend_services'][$_GET['service']])){
			$_POST['errors'][] = 'Impossible de modifier par ce service.';
		}else{
			$service->name = $_POST['service'];
			$service->check_data();
			if(!isset($_POST['errors'][0])){
				$_POST = array_merge($_POST, $service->save());

				$_SESSION['cached_backend_services'][$_GET['service']] = get_services_sorted_by_id($backend->type);
			}
		}
	}
}

if(!isset($_POST['service'])){
	$_POST['service'] = '';
}

if(!isset($_POST['services'])){
	$_POST['services'] = array();
}

if(!isset($_SESSION['cached_search_term'])){
	$_SESSION['cached_search_term'] = '';
}

$smarty->assign('service', $service);

$SUBTEMPLATE = 'services/backend_edit.tpl';

