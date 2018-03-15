<?php

use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$TITLE .= ' - Actualité';

$services = array();
$categories = array();

$idcategories = get_categories_sorted_by_id();

$plugin = Plugin::get_plugin(array('codename' => 'isou'));

$categories = array();
foreach (get_categories() as $category) {
    $category->services = array();
    $categories[$category->id] = $category;
}

$options = array();
$options['tolerance'] = $plugin->settings->tolerance;
$options['plugin'] = PLUGIN_ISOU;
$options['finished'] = false;

$events = get_events($options);
foreach ($events as $event) {
    if (isset($services[$event->idservice]) === false) {
        $service = get_service(array('id' => $event->idservice, 'visible' => true, 'plugin' => PLUGIN_ISOU));

        if ($service === false) {
            continue;
        }

        $services[$event->idservice] = $service;
    }

    $service = $services[$event->idservice];

    // on ne garde que les services ISOU non fermés
    if ($service->state === State::CLOSED) {
        continue;
    }

    if (isset($categories[$service->idcategory]->services[$event->idservice]) === false) {
        // initialise la categorie
        if (isset($categories[$service->idcategory]) === false) {
            continue;
        }

        // ajoute le service à la catégorie
        $categories[$service->idcategory]->services[$event->idservice] = $service;
        $categories[$service->idcategory]->services[$event->idservice]->events = array();
    }

    // ajoute l'évènement au service
    $categories[$service->idcategory]->services[$event->idservice]->events[] = $event;
}

foreach ($categories as $idcategory => $category) {
    if (count($category->services) === 0) {
        unset($categories[$idcategory]);
    }
}

$smarty->assign('categories', $categories);

$TEMPLATE = 'public/news.tpl';
