<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$services = array();
foreach (Service::get_records() as $record) {
    $services[$record->id] = $record;
}

$dependencies = array();
$dependency_groups = Dependency_Group::get_records(array('service' => $service->id));
foreach ($dependency_groups as $dependency_group) {
    $contents = $dependency_group->get_contents();
    foreach ($contents as $content) {
        $dependencies[$content->idservice] = $services[$content->idservice]->name;
    }
}
asort($dependencies);

if (count($dependencies) === 0) {
    $_SESSION['messages']['errors'] = array('Il n\'est pas possible de faire une simulation sur un service sans dépendance.');
    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
}

if (isset($_POST['state']) === true && is_array($_POST['state']) === true) {
    $DB->beginTransaction();

    foreach ($_POST['state'] as $idservice => $state) {
        $services[$idservice]->state = $state;
        $services[$idservice]->save();
    }

    $service->state = State::OK;
    $service->save();

    $results = array();
    foreach ($dependency_groups as $dependency_group) {
        $state = $dependency_group->groupstate;
        if (isset($results[$state]) === false) {
            $results[$state] = array();
        }

        foreach ($dependency_group->contents as $content) {
            $content->servicestate = $services[$content->idservice]->state;
        }

        if ($dependency_group->is_up() === true) {
            $dependency_group->groupstate = State::OK;
        } elseif ($dependency_group->groupstate > $service->state) {
            $service->state = $dependency_group->groupstate;
            $service->save();
        }

        $results[$state][] = $dependency_group;
    }

    $DB->rollBack();

    $smarty->assign('results', $results);
}

$options_states = array();
foreach (State::get_records() as $state) {
    if (in_array($state->id, array(State::OK, State::WARNING, State::CRITICAL), $strict = true) === false) {
        continue;
    }

    $options_states[$state->id] = sprintf('%s %s', State::$UNICODE[$state->id], State::$STATES[$state->id]);
}

$smarty->assign('dependencies', $dependencies);
$smarty->assign('options_states', $options_states);
$smarty->assign('service', $service);

$smarty->registerClass('State', 'UniversiteRennes2\Isou\State');

$TEMPLATE = 'dependencies/simulate.tpl';
