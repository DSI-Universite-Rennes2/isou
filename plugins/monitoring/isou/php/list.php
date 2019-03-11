<?php

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Service;

$categories = array();
foreach (Category::get_records() as $category) {
    $categories[$category->id] = new stdClass();
    $categories[$category->id]->name = $category->name;
    $categories[$category->id]->services = array();
}

$services = Service::get_records(array('plugin' => PLUGIN_ISOU));
foreach ($services as $service) {
    $service->notes = array();

    if ($service->visible === '0') {
        $service->notes[] = 'Service masqué sur les pages publiques';
    }

    if ($service->locked === '1') {
        $service->notes[] = 'Service dont l\'état est verrouillé';
    }

    $service->get_dependencies();
    if (isset($service->dependencies[0]) === false) {
        $service->notes[] = 'Service sans dépendance';
    }

    $categories[$service->idcategory]->services[] = $service;
}

$smarty->assign('categories', $categories);

$SUBTEMPLATE = 'list.tpl';
