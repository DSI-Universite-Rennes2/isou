<?php

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Dependency_Group_Content;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$dependency_group = Dependency_Group::get_record(array('id' => $PAGE_NAME[4]));

if ($dependency_group === false) {
    $_SESSION['messages'] = array('errors' => array('Ce contenu n\'existe pas.'));

    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
}

$dependency_group_content = Dependency_Group_Content::get_record(array('id' => $PAGE_NAME[7]));
if ($dependency_group_content === false) {
    $dependency_group_content = new Dependency_Group_Content();
}

$options_states = array(
    State::WARNING => State::$STATES[State::WARNING],
    State::CRITICAL => State::$STATES[State::CRITICAL],
    );

$plugins = array();
foreach (Plugin::get_records(['active' => true]) as $plugin) {
    $plugins[$plugin->id] = $plugin->name;
}

$options_services = array();
foreach (Service::get_records() as $option) {
    if (isset($plugins[$option->idplugin]) === false) {
        continue;
    }

    $plugin_name = $plugins[$option->idplugin];
    if (isset($options_services[$plugin_name]) === false) {
        $options_services[$plugin_name] = array();
    }

    // TODO: supprimer les services déjà présent dans les groupes du même état.
    $options_services[$plugin_name][$option->id] = $option->name;
}

if (isset($_POST['services'], $_POST['servicestate']) === true) {
    if ($options_states[$_POST['servicestate']] === false) {
        $_POST['errors'][] = 'Le champ "État du service lié" est invalide.';
    }

    if (is_array($_POST['services']) === false) {
        $children = array($_POST['services']);
    } else {
        $children = $_POST['services'];
    }

    if (empty($children) === true) {
        $_POST['errors'][] = 'Le champ "État du service lié" est invalide.';
    } else {
        foreach ($children as $child) {
            if (ctype_digit($child) === false) {
                $_POST['errors'][] = 'Le champ "État du service lié" est invalide.';
                break;
            }

            if (Service::get_record(array('id' => $child)) === false) {
                $_POST['errors'][] = 'Le champ "État du service lié" est invalide.';
                break;
            }
        }
    }

    if (isset($_POST['errors'][0]) === false) {
        $update = ($dependency_group_content->idservice !== 0);

        foreach ($children as $child) {
            if ($update === true) {
                $dependency_group_content->old_values = new stdClass();
                $dependency_group_content->old_values->idgroup = $dependency_group_content->idgroup;
                $dependency_group_content->old_values->idservice = $dependency_group_content->idservice;
                $dependency_group_content->old_values->servicestate = $dependency_group_content->servicestate;
            }

            $dependency_group_content->idgroup = $dependency_group->id;
            $dependency_group_content->idservice = $child;
            $dependency_group_content->servicestate = $_POST['servicestate'];

            $_POST = array_merge($_POST, $dependency_group_content->save());

            if ($update === false) {
                // Empêche un changement de type de formulaire si l'enregistrement des données se passe mal.
                $dependency_group_content->idservice = 0;
            }

            if (isset($_POST['errors'][0]) !== false) {
                break;
            }
        }
    }

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages'] = $_POST;

        header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
        exit(0);
    }
}

$smarty->assign('options_states', $options_states);
$smarty->assign('options_services', $options_services);

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);
$smarty->assign('dependency_group_content', $dependency_group_content);

$TEMPLATE = 'dependencies/contents/edit.tpl';
