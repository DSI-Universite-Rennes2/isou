<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

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
    $categories[$category->id] = new stdClass();
    $categories[$category->id]->name = $category->name;
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
    $item = new stdClass();
    $item->name = $service->name;
    $item->url = $service->url;
    $item->state = $service->state;
    $item->events = array();

    if ($service->is_closed === true) {
        $item->closed_event = $service->get_closed_event();
        $item->events = Event::get_records(array('finished' => false, 'idservice' => $service->id, 'type' => Event::TYPE_CLOSED));
    } else {
        $item->count_unscheduled_events = 0;
        foreach (Event::get_records(array('since' => $since, 'idservice' => $service->id, 'tolerance' => $tolerance)) as $index => $event) {
            if ($event->startdate >= $now && $event->type === Event::TYPE_SCHEDULED) {
                $categories[$service->idcategory]->scheduled_events_count++;

                // Un évènement prévu en cours ou à venir.
                if (empty($event->enddate) === true || $event->enddate > $now) {
                    $item->scheduled_events = true;
                }
            } elseif ($event->enddate < $now && $event->type === Event::TYPE_UNSCHEDULED) {
                $categories[$service->idcategory]->past_events_count++;
                $item->count_unscheduled_events++;
            }

            // Limite à 3, le nombre d'évènements affichés par défaut.
            if ($item->count_unscheduled_events <= 3) {
                $item->events[] = $event;
            } else {
                $item->more[] = $event;
            }
        }

        $item->regular_events = $service->get_regular_events();

        // Modifie le drapeau de la catégorie au plus haut niveau d'alerte.
        if ($categories[$service->idcategory]->state < $service->state) {
            $categories[$service->idcategory]->state = $service->state;
        }

        // Affiche par défaut uniquement les service instables et les évènements en cours ou à venir.
        if ($service->state !== State::OK || isset($service->scheduled_events) === true) {
            $categories[$service->idcategory]->unstable_services[] = $item;
        }
    }

    $categories[$service->idcategory]->services[] = $item;
}

$smarty->assign('categories', $categories);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/list/html');

$smarty->registerClass('Event', 'UniversiteRennes2\Isou\Event');
$smarty->registerClass('State', 'UniversiteRennes2\Isou\State');

$TEMPLATE = 'view.tpl';
