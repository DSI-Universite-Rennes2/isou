<?php

use UniversiteRennes2\Isou\Event;

$event = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $event = get_event(array('id' => $PAGE_NAME[3]));
}

if ($event === false) {
    $_SESSION['messages'] = array('errors' => 'Cet évènement n\'existe pas.');

    header('Location: '.URL.'/index.php/evenements');
    exit(0);
} elseif (isset($_POST['delete']) === true) {
    // Valide la suppression de l'évènement.
    $_POST['errors'] = array();

    try {
        $event->delete();
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
