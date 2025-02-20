<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Event_Description;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$event = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $event = Event::get_record(array('id' => $PAGE_NAME[3]));
}

if ($event === false) {
    $event = new Event();

    // Essaye de positionner correctement le type d'évènement dans le ménu déroulant.
    switch ($PAGE_NAME[1]) {
        case 'fermes':
            $event->set_type(Event::TYPE_CLOSED);

            $event->set_state(State::CLOSED);
            $_POST['state'] = State::CLOSED;
            break;
        case 'imprevus':
            $event->set_type(Event::TYPE_UNSCHEDULED);
            break;
        case 'reguliers':
            $event->set_type(Event::TYPE_REGULAR);
            break;
        case 'prevus':
        default:
            $event->set_type(Event::TYPE_SCHEDULED);
    }
}

if (isset($_POST['type']) === false) {
    $_POST['type'] = $event->type;
}

$options_states = State::$STATES;

$options_periods = Event::$PERIODS;

$options_services = Service::get_records(array('fetch_column' => true, 'plugin' => PLUGIN_ISOU, 'has_category' => true));

$options_types = Event::$TYPES;
if (in_array($event->type, array(Event::TYPE_SCHEDULED, Event::TYPE_UNSCHEDULED), $strict = true) === true) {
    unset($options_types[Event::TYPE_CLOSED], $options_types[Event::TYPE_REGULAR]);
}

if ($_POST['type'] === Event::TYPE_CLOSED) {
    $_POST['state'] = State::CLOSED;
}

if (isset($_POST['type'], $_POST['service'], $_POST['startdate'], $_POST['starttime'], $_POST['enddate'], $_POST['endtime'], $_POST['state'], $_POST['description']) === true) {
    $_POST['errors'] = array();

    try {
        $event->set_type($_POST['type']);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    try {
        $event->set_service($_POST['service'], $options_services);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    try {
        $event->set_state($_POST['state'], $options_states);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    try {
        $event->set_startdate($_POST['startdate'], $_POST['starttime']);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    try {
        $event->set_enddate($_POST['enddate'], $_POST['endtime']);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    if (isset($_POST['period']) === false) {
        $_POST['period'] = Event::PERIOD_NONE;
    }

    try {
        $event->set_period($_POST['period']);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    try {
        $event->set_description($_POST['description'], $autogen = false);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    if (isset($_POST['errors'][0]) === false) {
        $DB->beginTransaction();

        try {
            // Enregistre l'évènement en base de données et sa description.
            $event->save();

            $service = Service::get_record(array('id' => $event->idservice, 'plugin' => PLUGIN_ISOU));

            if ($_POST['type'] === Event::TYPE_CLOSED && $event->is_now() === true) {
                $service->state = State::CLOSED;
                $service->save();
            }

            if ($_POST['type'] === Event::TYPE_UNSCHEDULED) {
                if (empty($event->enddate) === true) {
                    // L'évènement n'a pas de date de fin. On verrouille le service pour que le CRON ne ferme pas automatiquement l'évènement.
                    if ($service->is_locked() === false) {
                        $service->lock($event->state);
                    }
                } else {
                    // L'évènement a une date de fin. On déverrouille le service au besoin.
                    if ($service->is_locked() === true) {
                        $service->unlock();
                    }
                }
            }

            $DB->commit();
        } catch (Exception $exception) {
            $DB->rollBack();
            $_POST['errors'] = array($exception->getMessage());
        }

        if (isset($_POST['errors'][0]) === false) {
            $_SESSION['messages'] = array('successes' => 'L\'évènement a été enregistré.');

            switch ($event->type) {
                case Event::TYPE_CLOSED:
                    header('Location: '.URL.'/index.php/evenements/fermes');
                    break;
                case Event::TYPE_REGULAR:
                    header('Location: '.URL.'/index.php/evenements/reguliers');
                    break;
                case Event::TYPE_UNSCHEDULED:
                    header('Location: '.URL.'/index.php/evenements/imprevus');
                    break;
                case Event::TYPE_SCHEDULED:
                default:
                    header('Location: '.URL.'/index.php/evenements/prevus');
            }

            exit(0);
        }
    }
}

$smarty->assign('options_states', $options_states);
$smarty->assign('options_periods', $options_periods);
$smarty->assign('options_services', $options_services);
$smarty->assign('options_types', $options_types);

$smarty->assign('event', $event);

$smarty->registerClass('Event', 'UniversiteRennes2\Isou\Event');
$smarty->registerClass('State', 'UniversiteRennes2\Isou\State');

$subtemplate = 'events/edit.tpl';
