<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Dependency_Group_Content;
use UniversiteRennes2\Isou\Event;

$TITLE .= ' - Évènements en cours';

$event = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $event = Event::get_record(array('id' => $PAGE_NAME[3]));
}

if ($event === false) {
    $_SESSION['messages'] = array('errors' => 'Cet évènement n\'existe pas.');

    header('Location: '.URL.'/index.php/evenements/'.$PAGE_NAME[1]);
    exit(0);
}

$events = array($event);

// TODO: récupérer les services de services (Isou).
$groups = Dependency_Group::get_records(array('service' => $event->idservice));
foreach ($groups as $group) {
    $contents = Dependency_Group_Content::get_records(array('group' => $group->id));
    foreach ($contents as $content) {
        if ($event->enddate === null) {
            $dependency_events = Event::get_records(array('idservice' => $content->idservice, 'finished' => false));
        } else {
            $dependency_events = Event::get_records(array('idservice' => $content->idservice, 'enddate_between' => array($event->startdate, $event->enddate)));
        }

        foreach ($dependency_events as $dependency_event) {
            $events[$dependency_event->id] = $dependency_event;
        }
    }
}

$plugins_ = array();
foreach ($plugins as $plugin) {
    $plugins_[$plugin->id] = $plugin;
}
$smarty->assign('plugins', $plugins_);

$smarty->assign('events', $events);

$subtemplate = 'events/more.tpl';
