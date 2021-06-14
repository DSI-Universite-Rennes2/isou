<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Dependency_Group_Content;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$dependency_group = Dependency_Group::get_record(array('id' => $PAGE_NAME[5]));

if ($dependency_group === false) {
    $_SESSION['messages'] = array('errors' => array('Ce groupe n\'existe pas.'));

    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
}

if (isset($_POST['duplicate']) === true) {
    $_POST = array_merge($_POST, $dependency_group->duplicate());

    if (isset($_POST['errors'][0]) === false) {
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

    foreach (Dependency_Group_Content::get_records(array('group' => $dependency_group->id)) as $content) {
        $child = Service::get_record(array('id' => $content->idservice));
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
