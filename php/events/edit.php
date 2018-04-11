<?php

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\EventDescription;
use UniversiteRennes2\Isou\State;

$event = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $event = get_event($PAGE_NAME[3]);
}

if ($event === false) {
    $event = new Event();

    // Essaye de positionner correctement le type d'évènement dans le ménu déroulant.
    if (isset($_POST['type']) === false) {
        switch ($PAGE_NAME[1]) {
            case 'fermes':
                $event->set_type(Event::TYPE_CLOSED);
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
}

$options_states = State::$STATES;

$options_periods = Event::$PERIODS;

$options_services = get_isou_services_sorted_by_idtype();

$options_types = Event::$TYPES;

if (isset($_POST['type'], $_POST['service'], $_POST['startdate'], $_POST['starttime'], $_POST['enddate'], $_POST['endtime'], $_POST['period'], $_POST['description']) === true) {
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

    try {
        $event->set_period($_POST['period']);
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    $event_description = get_event_description_by_content($_POST['description']);
    if ($event_description === false) {
        $event_description = new EventDescription();
        $event_description->description = $_POST['description'];
        $event_description->autogen = 0;
    }

    if (isset($_POST['errors'][0]) === false) {
        $DB->beginTransaction();

        try {
            if ($event_description->id === 0) {
                $event_description->save();
                $event->ideventdescription = $event_description->id;
            }

            $event->save();

            if ($_POST['type'] === Event::TYPE_CLOSED && $event->is_now() === true) {
                $service = get_service(array('id' => $event->idservice, 'plugin' => PLUGIN_ISOU));
                $service->state = State::CLOSED;
                $service->save();
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

$subtemplate = 'events/edit.tpl';
