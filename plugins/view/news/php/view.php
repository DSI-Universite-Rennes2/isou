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
    $TITLE .= ' - Actualité';
}

$services = array();
$categories = array();

$plugin = Plugin::get_record(array('id' => PLUGIN_ISOU));

$categories = array();
$idcategories = array();
foreach (Category::get_records() as $category) {
    $category->services = array();
    $categories[$category->id] = $category;
    $idcategories[$category->id] = $category->name;
}

$options = array();
$options['tolerance'] = $plugin->settings->tolerance;
$options['plugin'] = PLUGIN_ISOU;
$options['finished'] = false;

$events = Event::get_records($options);
foreach ($events as $event) {
    if (isset($services[$event->idservice]) === false) {
        $service = Service::get_record(array('id' => $event->idservice, 'visible' => true, 'plugin' => PLUGIN_ISOU));

        if ($service === false) {
            continue;
        }

        $services[$event->idservice] = $service;
    }

    $service = $services[$event->idservice];

    // On ne garde que les services ISOU non fermés.
    if ($service->state === State::CLOSED) {
        continue;
    }

    // On n'affiche pas les évènements à venir.
    if ($event->startdate > new DateTime()) {
        continue;
    }

    if (isset($categories[$service->idcategory]->services[$event->idservice]) === false) {
        // Initialise la categorie.
        if (isset($categories[$service->idcategory]) === false) {
            continue;
        }

        // Ajoute le service à la catégorie.
        $categories[$service->idcategory]->services[$event->idservice] = $service;
        $categories[$service->idcategory]->services[$event->idservice]->events = array();
    }

    // Ajoute l'évènement au service.
    $categories[$service->idcategory]->services[$event->idservice]->events[] = $event;
}

foreach ($categories as $idcategory => $category) {
    if (count($category->services) === 0) {
        unset($categories[$idcategory]);
    }
}

$smarty->assign('categories', $categories);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/news/html');
$TEMPLATE = 'view.tpl';
