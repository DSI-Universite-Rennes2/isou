<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;

$event = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $event = Event::get_record(array('id' => $PAGE_NAME[3]));
}

if ($event === false) {
    $_SESSION['messages'] = array('errors' => 'Cet évènement n\'existe pas.');

    header('Location: '.URL.'/index.php/evenements/'.$PAGE_NAME[1]);
    exit(0);
} elseif (isset($_POST['delete']) === true) {
    // Valide la suppression de l'évènement.
    $_POST['errors'] = array();

    try {
        $event->delete();

        $service = Service::get_record(array('id' => $event->idservice, 'plugin' => PLUGIN_ISOU));
        if ($event->type === Event::TYPE_UNSCHEDULED && empty($event->enddate) === true && $service->is_locked() === true) {
            $service->unlock();
        }
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages'] = array('successes' => 'L\'évènement a été supprimé.');

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

$smarty->assign('event', $event);

$subtemplate = 'events/delete.tpl';
