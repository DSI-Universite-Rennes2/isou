<?php

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Dependency;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$TITLE .= ' - Administration des dépendances';

if (isset($PAGE_NAME[1]) === false) {
    $PAGE_NAME[1] = '';
}

if ($PAGE_NAME[1] === 'service' && isset($PAGE_NAME[2]) === true && ctype_digit($PAGE_NAME[2]) === true) {
    $service = Service::get_record(array('id' => $PAGE_NAME[2], 'plugin' => PLUGIN_ISOU));

    if ($service === false) {
        $_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

        header('Location: '.URL.'/index.php/dependances');
        exit(0);
    }

    if (isset($PAGE_NAME[4]) === true && $PAGE_NAME[3] === 'group') {
        if (isset($PAGE_NAME[7]) === true && ctype_digit($PAGE_NAME[7]) === true && $PAGE_NAME[5] === 'content' && ctype_digit($PAGE_NAME[4]) === true) {
            // Group contents.
            switch ($PAGE_NAME[6]) {
                case 'delete':
                    require PRIVATE_PATH.'/php/dependencies/contents/delete.php';
                    break;
                case 'edit':
                default:
                    require PRIVATE_PATH.'/php/dependencies/contents/edit.php';
            }
        } else if (isset($PAGE_NAME[5]) === true && ctype_digit($PAGE_NAME[5]) === true) {
            // Groups.
            switch ($PAGE_NAME[4]) {
                case 'delete':
                    require PRIVATE_PATH.'/php/dependencies/groups/delete.php';
                    break;
                case 'duplicate':
                    require PRIVATE_PATH.'/php/dependencies/groups/duplicate.php';
                    break;
                case 'edit':
                default:
                    require PRIVATE_PATH.'/php/dependencies/groups/edit.php';
            }
        }
    }

    if (isset($TEMPLATE) === false) {
        // Affiche les groupes d'un service.
        require PRIVATE_PATH.'/php/dependencies/groups/list.php';
    }
}

if (isset($TEMPLATE) === false) {
    // Affiche la liste des services.
    $categories = array();
    foreach (Category::get_records(array('non-empty' => true)) as $category) {
        $categories[$category->id] = $category;
        $categories[$category->id]->services = array();
    }

    $services = Service::get_records(array('plugin' => PLUGIN_ISOU));
    foreach ($services as $service) {
        $service->count_warning_groups = 0;
        $service->count_critical_groups = 0;

        foreach ($service->get_dependencies() as $dependency) {
            switch ($dependency->groupstate) {
                case State::WARNING:
                    $service->count_warning_groups++;
                    break;
                case State::CRITICAL:
                    $service->count_critical_groups++;
            }
        }

        $categories[$service->idcategory]->services[] = $service;
    }

    $smarty->assign('categories', $categories);

    $TEMPLATE = 'dependencies/index.tpl';
}
