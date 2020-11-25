<?php

use UniversiteRennes2\Isou\Service;

// Charge la liste des services Thruk distants.
$sources = array();
$cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/thruk';
if (is_readable($cache_path.'/services.json') === true) {
    $cache = file_get_contents($cache_path.'/services.json');
    $cache = json_decode($cache, $array = true);

    if (empty($cache) === false) {
        foreach ($cache as $data) {
            $sources[$data['name']] = $data['name'];
        }
    }
}

// Charge les services Thruk enregistrés dans la base de données Isou.
$services = Service::get_records(array('plugin' => $plugin->id));

foreach ($services as $service) {
    $service->notes = array();

    $service->set_reverse_dependencies();
    if (isset($service->reverse_dependencies[0]) === false) {
        $service->notes[] = 'Service non rattaché à un service Isou';
    }

    if (isset($sources[$service->name]) === false) {
        $service->notes[] = 'Service retiré du serveur Thruk';
    }
}

$smarty->assign('services', $services);

$SUBTEMPLATE = 'list.tpl';
