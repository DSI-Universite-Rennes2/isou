<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Service;

// Charge la liste des services Zabbix distants.
$sources = array();
$cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/zabbix';
if (is_readable($cache_path.'/services.json') === true) {
    $cache = file_get_contents($cache_path.'/services.json');
    $cache = json_decode($cache, $array = true);

    if (empty($cache) === false) {
        foreach ($cache as $data) {
            $sources[$data['name']] = $data['name'];
        }
    }
}

// Charge les services Zabbix enregistrés dans la base de données Isou.
$services = Service::get_records(array('plugin' => $plugin->id));

foreach ($services as $service) {
    $service->notes = array();

    if (isset($sources[$service->name]) === false) {
        $service->notes[] = array('label' => 'Service retiré du serveur Zabbix', 'style' => 'danger');
    }

    $service->set_reverse_dependencies();
    if (isset($service->reverse_dependencies[0]) === false) {
        $service->notes[] = array('label' => 'Service non rattaché à un service Isou', 'style' => 'warning');
    }
}

$smarty->assign('services', $services);

$SUBTEMPLATE = 'list.tpl';
