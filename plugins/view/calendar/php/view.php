<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

if (count($MENUS->public) > 1) {
    $TITLE .= ' - Calendrier';
}

$_GET['page'] = '1';
if (isset($PAGE_NAME[1]) === true && ctype_digit($PAGE_NAME[1]) === true) {
    if ($PAGE_NAME[1] === '0') {
        $_GET['page'] = '1';
    } elseif ($PAGE_NAME[1] > 5) {
        $_GET['page'] = '5';
    } else {
        $_GET['page'] = $PAGE_NAME[1];
    }
}

$date = getdate();

$CALENDAR_STEP = 'MONTHLY'; // TODO: faire une option pour gérer la navigation par semaine (WEEKLY).

if ($CALENDAR_STEP === 'WEEKLY') {
    $time = mktime(0, 0, 0) - ((6 + $date['wday'] - (($_GET['page'] - 1) * 7))) * 24 * 60 * 60;
} else {
    $first_months_day = mktime(0, 0, 0, $date['mon'] + $_GET['page'] - 1, 1);
    $time = $first_months_day - ((intval(strftime('%u', $first_months_day)) - 1) * 24 * 60 * 60);
}

$begincalendar = strftime('%Y-%m-%dT%H:%M', $time);
$endcalendar = strftime('%Y-%m-%dT%H:%M', $time + 35 * 24 * 60 * 60);

$one_day = new DateInterval('P1D');
$calendar = array();
$calendar_day = new stdClass();
$calendar_day->datetime = $time;
$calendar_day->services = array();
for ($i = 0; $i < 5; $i++) {
    for ($j = 0; $j < 7; $j++) {
        if ($i === 0) {
            if (strftime('%d', $calendar_day->datetime) === '01') {
                if (strftime('%m', $calendar_day->datetime) === '01') {
                    $calendar_day->strftime = '1er %B %Y';
                } else {
                    $calendar_day->strftime = '1er %B';
                }
            } else {
                $calendar_day->strftime = '%d %B';
            }
        } else {
            $calendar_day->strftime = '%d';
        }

        $calendar[$i][$j] = clone($calendar_day);
        $calendar_day->datetime += 24 * 60 * 60;
    }
}

$begincalendar = new DateTime($begincalendar);
$endcalendar = new DateTime($endcalendar);

$plugin = Plugin::get_record(array('id' => PLUGIN_ISOU));

$options = array();
$options['tolerance'] = $plugin->settings->tolerance;
$options['plugin'] = PLUGIN_ISOU;
$options['type'] = Event::TYPE_SCHEDULED;
$options['since'] = $begincalendar;

$events = Event::get_records($options);
foreach ($events as $event) {
    $service = Service::get_record(array('id' => $event->idservice, 'visible' => true));

    if ($service === false) {
        continue;
    }

    if ($event->state === State::CLOSED) {
        continue;
    }

    if ($event->enddate === null) {
        if ($_GET['page'] !== '1') {
            continue;
        }
    } elseif (($event->startdate >= $begincalendar && $event->enddate <= $endcalendar) === false) {
        continue;
    }

    $event->service = $service->name;
    $service->event = $event;

    $startdate = clone $event->startdate;
    $startdate->setTime(0, 0, 0);
    $interval = $begincalendar->diff($startdate);

    if ($interval->invert === 1) {
        $startdate = clone $begincalendar;
        $i = 0;
        $j = 0;
    } else {
        $i = round($interval->d / 5, 0) - 1;
        if ($i < 0) {
            $i = 0;
        }
        $j = $interval->d % 7;
    }

    if ($event->enddate === null) {
        $enddate = new DateTime();
    } else {
        $enddate = clone $event->enddate;
    }

    while ($startdate < $enddate) {
        if (isset($calendar[$i][$j]) === false) {
            break;
        }

        $calendar[$i][$j]->services[] = $service;
        $startdate->add($one_day);

        $j++;
        if ($j > 6) {
            $j = 0;
            $i++;
        }
    }
}

$smarty->assign('calendar', $calendar);
$smarty->assign('now', mktime(0, 0, 0));

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/calendar/html');
$TEMPLATE = 'view.tpl';
