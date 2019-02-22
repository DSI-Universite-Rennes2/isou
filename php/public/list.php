<?php

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$TITLE .= ' - Liste';

$since = new DateTime();
$since->sub(new DateInterval('P2D')); // TODO: create CFG variable.

$categories = array();
foreach (Category::get_records(array('non-empty' => true, 'only-visible-services' => true)) as $category) {
    $categories[$category->id] = $category;
    $categories[$category->id]->state = State::OK;
    $categories[$category->id]->unstable_services = array();

    $categories[$category->id]->services = array();
}

$services = Service::get_records(array('plugin' => PLUGIN_ISOU, 'visible' => true));

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
        $service->events = array();
        foreach (Event::get_records(array('since' => $since, 'idservice' => $service->id)) as $index => $event) {
            if ($index < 3) {
                $service->events[] = $event;
            } else {
                $service->more[] = $event;
            }
        }

        $service->regular_events = $service->get_regular_events();

        if ($categories[$service->idcategory]->state < $service->state) {
            $categories[$service->idcategory]->state = $service->state;
        }

        if ($service->state !== State::OK) {
            $categories[$service->idcategory]->unstable_services[] = $service;
        }
    }

    $categories[$service->idcategory]->services[] = $service;
}

$smarty->assign('categories', $categories);

$TEMPLATE = 'public/list.tpl';
