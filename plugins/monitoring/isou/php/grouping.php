<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

$isou = Plugin::get_record(array('id' => PLUGIN_ISOU));
if ($isou->settings->grouping === false) {
    header('Location: '.URL.'/index.php/services/isou');
    exit(0);
}

$services = Service::get_records(array('plugin' => PLUGIN_ISOU, 'has_category' => false));
foreach ($services as $service) {
    $service->dependencies_content = array();

    foreach ($service->get_dependencies() as $dependency) {
        // On récupère la liste des services présents dans le groupe de dépendances.
        $service->dependencies_content = $dependency->get_contents();

        // Les 2 groupes de dépendances étant identiques, on ne parse pas le second groupe.
        break;
    }
}

$smarty->assign('plugins', Plugin::get_records(array('fetch_column' => true)));
$smarty->assign('services', $services);

$SUBTEMPLATE = 'grouping.tpl';
