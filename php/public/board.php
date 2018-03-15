<?php

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$TITLE .= ' - Tableau';

$today = new DateTime();

$categories = array();
foreach (get_categories() as $category) {
    $categories[$category->id] = $category;
    $categories[$category->id]->services = array();
}


$services_events = array();

$days = array();
for ($i = 0; $i < 7; $i++) {
    $days[$i] = new DateTime(strftime('%Y-%m-%d', TIME - ($i * 24 * 60 * 60)));

    $since = clone $days[$i];
    $before = clone $days[$i];

    $options = array();
    $options['since'] = $since->setTime(0, 0, 0);
    $options['before'] = $before->setTime(23, 59, 60);
    $options['plugin'] = PLUGIN_ISOU;

    $events = get_events($options);
    foreach ($events as $event) {
        if (!isset($services_events[$event->idservice])) {
            $services_events[$event->idservice] = array();
        }

        if (!isset($services_events[$event->idservice][$i])) {
            $services_events[$event->idservice][$i] = 0;
        }

        if ($event->startdate < $options['since']) {
            $event->startdate = $options['since'];
        }

        if ($event->enddate === null || $event->enddate > $options['before']) {
            if ($i === 0) {
                $event->enddate = $today;
            } else {
                $event->enddate = $options['before'];
            }
        }

        $services_events[$event->idservice][$i] += $event->enddate->getTimestamp() - $event->startdate->getTimestamp();
    }
}

$services = get_services(array('plugin' => PLUGIN_ISOU, 'visible' => true));
foreach ($services as $service) {
    if ($service->enable === '0' || $service->visible === '0') {
        continue;
    }

    // changement de categorie
    if (isset($categories[$service->idcategory]) === false) {
        continue;
    }

    // ajout des évènements
    $service->events = array();
    $service->availabilities = array();
    $service->availabilities_total = 0;

    for ($i = 0; $i < 7; $i++) {
        if (isset($services_events[$service->id][$i])) {
            if ($i === 0) {
                $base = time() - mktime(0, 0, 0);
            } else {
                $base = 86400;
            }

            $elapsed_time = $base - $services_events[$service->id][$i];
            $service->availabilities[$i] = ceil($elapsed_time / $base * 100);
            if ($service->availabilities[$i] > 100) {
                $service->availabilities[$i] = 100;
            }
        } else {
            $service->availabilities[$i] = 100;
        }

        $service->availabilities_total += $service->availabilities[$i];
    }

    $service->availabilities_total = ceil($service->availabilities_total / 7);

    $categories[$service->idcategory]->services[] = $service;
}

$smarty->assign('days', $days);
$smarty->assign('categories', $categories);

$TEMPLATE = 'public/board.tpl';
