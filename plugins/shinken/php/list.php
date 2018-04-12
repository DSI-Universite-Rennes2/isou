<?php

use UniversiteRennes2\Isou\Service;

$services = Service::get_records(array('plugin' => $plugin->id));

foreach ($services as $service) {
    $service->notes = array();

    $service->set_reverse_dependencies();
    if (isset($service->reverse_dependencies[0]) === false) {
        $service->notes[] = 'Service non rattaché à un service Isou';
    }
}

$smarty->assign('services', $services);

$SUBTEMPLATE = 'list.tpl';
