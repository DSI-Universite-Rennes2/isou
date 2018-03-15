<?php

use UniversiteRennes2\Isou\Service;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$TITLE .= ' - Liste';

$since = new DateTime();
$since->sub(new DateInterval('P2D')); // TODO: create CFG variable.

$categories = array();
foreach (get_categories(array('non-empty' => true)) as $category) {
    $categories[$category->id] = $category;
    $categories[$category->id]->services = array();
}

$services = get_services(array('plugin' => PLUGIN_ISOU, 'visible' => true));

foreach ($services as $service) {
    if ($service->enable === '0' || $service->visible === '0') {
        continue;
    }

    // Changement de categorie.
    if (isset($categories[$service->idcategory]) === false) {
        continue;
    }

    // Ajout des évènements.
    if ($service->is_closed === true) {
        $service->closed_event = $service->get_closed_event();
    } else {
        $service->events = get_events(array('since' => $since, 'idservice' => $service->id));
        $service->regular_events = $service->get_regular_events();
    }

    $categories[$service->idcategory]->services[] = $service;
}

$smarty->assign('categories', $categories);

$TEMPLATE = 'public/list.tpl';
