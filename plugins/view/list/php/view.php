<?php

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

if (count($MENUS->public) > 1) {
    $TITLE .= ' - Liste';
}

$now = new DateTime();
$since = new DateTime();
$since->sub(new DateInterval('P2D')); // TODO: create CFG variable.

$plugin = Plugin::get_record(array('id' => PLUGIN_ISOU));
$tolerance = intval($plugin->settings->tolerance);

$categories = array();
foreach (Category::get_records(array('non-empty' => true, 'only-visible-services' => true)) as $category) {
    $categories[$category->id] = $category;
    $categories[$category->id]->state = State::OK;
    $categories[$category->id]->unstable_services = array();
    $categories[$category->id]->past_events_count = 0;
    $categories[$category->id]->scheduled_events_count = 0;

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
    $service->events = array();
    if ($service->is_closed === true) {
        $service->closed_event = $service->get_closed_event();
        $service->events = Event::get_records(array('finished' => false, 'idservice' => $service->id, 'type' => Event::TYPE_CLOSED));
    } else {
        $service->count_unscheduled_events = 0;
        foreach (Event::get_records(array('since' => $since, 'idservice' => $service->id, 'tolerance' => $tolerance)) as $index => $event) {
            if ($event->startdate >= $now && $event->type === Event::TYPE_SCHEDULED) {
                $categories[$service->idcategory]->scheduled_events_count++;
            } else if ($event->enddate < $now && $event->type === Event::TYPE_UNSCHEDULED) {
                $categories[$service->idcategory]->past_events_count++;
                $service->count_unscheduled_events++;
            }

            // Limite à 3, le nombre d'évènements affichés par défaut.
            if ($service->count_unscheduled_events <= 3) {
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

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/list/html');
$TEMPLATE = 'view.tpl';
