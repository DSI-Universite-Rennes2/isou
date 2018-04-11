<?php

use UniversiteRennes2\Isou\Event;

require PRIVATE_PATH.'/libs/dependencies.php';

$TITLE .= ' - Évènements en cours';

$event = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $event = get_event(array('id' => $PAGE_NAME[3]));
}

if ($event === false) {
    $_SESSION['messages'] = array('errors' => 'Cet évènement n\'existe pas.');

    header('Location: '.URL.'/index.php/evenements/'.$PAGE_NAME[1]);
    exit(0);
}

$events = array($event);

$groups = get_dependency_groups(array('service' => $event->idservice));
foreach ($groups as $group) {
    $contents = get_dependency_group_contents(array('group' => $group->id));
    foreach ($contents as $content) {
        if ($event->enddate === null) {
            $dependency_events = get_events(array('idservice' => $content->idservice, 'finished' => false));
        } else {
            $dependency_events = get_events(array('idservice' => $content->idservice, 'between' => array($event->startdate, $event->enddate)));
        }

        foreach ($dependency_events as $dependency_event) {
            $events[$dependency_event->id] = $dependency_event;
        }
    }
}

$smarty->assign('events', $events);

$subtemplate = 'events/more.tpl';
