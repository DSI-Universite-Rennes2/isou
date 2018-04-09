<?php

use UniversiteRennes2\Isou\State;

$dependency_group = get_dependency_group(array('id' => $PAGE_NAME[5]));

if ($dependency_group === false) {
    $_SESSION['messages'] = array('errors' => array('Ce groupe n\'existe pas.'));

    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
} elseif (isset($_POST['duplicate'])) {
    $_POST = array_merge($_POST, $dependency_group->duplicate());

    if (!isset($_POST['errors'][0])) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
        exit(0);
    }
} else {
    // Create preview.
    $preview = new stdClass();
    $preview->name = '';
    $preview->services = array();

    switch ($dependency_group->groupstate) {
        case State::WARNING:
            $preview->name = $STATES[State::CRITICAL].' '.$dependency_group->name;
            break;
        case State::CRITICAL:
            $preview->name = $STATES[State::WARNING].' '.$dependency_group->name;
            break;
        default:
            $_SESSION['messages'] = array('errors' => array('Une erreur est survenue.'));
            header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
            exit(0);
    }

    foreach (get_dependency_group_contents(array('group' => $dependency_group->id)) as $content) {
        $child = get_service(array('id' => $content->idservice));
        if ($child !== false) {
            switch ($content->servicestate) {
                case State::WARNING:
                    $preview->services[] = $STATES[State::CRITICAL].' '.$child->name;
                    break;
                case State::CRITICAL:
                    $preview->services[] = $STATES[State::WARNING].' '.$child->name;
            }
        }
    }

    $smarty->assign('preview', $preview);
}

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);

$TEMPLATE = 'dependencies/groups/duplicate.tpl';
