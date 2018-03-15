<?php

if (isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])) {
    $event = get_event($PAGE_NAME[2]);
} else {
    $event = false;
}

if ($event === false) {
    $_SESSION['messages'] = array('errors' => 'Cet évènement n\'existe pas.');

    header('Location: '.URL.'/index.php/evenements');
    exit(0);
} elseif (isset($_POST['delete'])) {
    $_POST['errors'] = array();

    try {
        $event->delete();
    } catch (Exception $exception) {
        $_POST['errors'][] = $exception->getMessage();
    }

    if (!isset($_POST['errors'][0])) {
        $_SESSION['messages'] = array('successes' => 'L\'évènement a été supprimé.');

        if ($event->type === UniversiteRennes2\Isou\Event::TYPE_UNSCHEDULED) {
            header('Location: '.URL.'/index.php/evenements/imprevus');
        } else {
            header('Location: '.URL.'/index.php/evenements/prevus');
        }
        exit(0);
    }
}

$smarty->assign('event', $event);

$TEMPLATE = 'events/delete.tpl';
