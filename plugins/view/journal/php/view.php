<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;

if (count($MENUS->public) > 1) {
    $TITLE .= ' - Journal';
}

$services = array();
foreach (Service::get_records(array('plugin' => PLUGIN_ISOU, 'visible' => true)) as $service) {
    $services[$service->id] = $service->name;
}

$plugin = Plugin::get_record(array('id' => PLUGIN_ISOU));

$options = array();
$options['tolerance'] = $plugin->settings->tolerance;
$options['plugin'] = PLUGIN_ISOU;

$days = array();
for ($i = 0; $i < 7; $i++) {
    $days[$i] = new stdClass();
    $days[$i]->date = new DateTime(date('Y-m-d 00:00:00', TIME - ($i * 24 * 60 * 60)));

    $options['since'] = $days[$i]->date;
    $options['before'] = clone $days[$i]->date;
    $options['before']->setTime(23, 59, 59);
    $days[$i]->events = array();
    foreach (Event::get_records($options) as $event) {
        if ($event->enddate === null && $event->startdate->format('Y-m-d') > $days[$i]->date->format('Y-m-d')) {
            continue;
        }

        if (isset($services[$event->idservice]) === false) {
            continue;
        }

        $event->service = $services[$event->idservice];

        $days[$i]->events[] = $event;
    }
}

$smarty->assign('days', $days);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/journal/html');
$TEMPLATE = 'view.tpl';
