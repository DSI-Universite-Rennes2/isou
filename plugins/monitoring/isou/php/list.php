<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;

$categories = array();
foreach (Category::get_records() as $category) {
    $categories[$category->id] = new stdClass();
    $categories[$category->id]->name = $category->name;
    $categories[$category->id]->services = array();
}

$services = Service::get_records(array('plugin' => PLUGIN_ISOU, 'has_category' => true));
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
$smarty->assign('isou', Plugin::get_record(array('id' => PLUGIN_ISOU)));

$SUBTEMPLATE = 'list.tpl';
