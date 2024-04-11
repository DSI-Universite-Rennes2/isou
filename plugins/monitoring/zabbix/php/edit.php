<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Service;

$service = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = Service::get_record(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_ZABBIX));
}

if ($service === false) {
    $service = new Service();
    $service->idplugin = PLUGIN_ZABBIX;
}

// Check cache.
$cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/zabbix';
if (is_readable($cache_path.'/services.json') === false) {
    $_SESSION['messages']['errors'][] = 'Le fichier "'.$cache_path.'/services.json" n\'existe pas ou ne peut être lu.<br />'.
       'Assurez-vous que le cron s\'exécute correctement.';
    header('Location: '.URL.'/index.php/services/zabbix');
    exit(0);
}

// Load cache.
$cache = file_get_contents($cache_path.'/services.json');
$cache = json_decode($cache, $array = true);

if ($cache === null) {
    $_SESSION['messages']['errors'][] = 'Le fichier "'.$cache_path.'/services.json" est corrompu.';
    header('Location: '.URL.'/index.php/services/zabbix');
    exit(0);
}

if (count($cache) === 0) {
    $_SESSION['messages']['errors'][] = 'Le fichier "'.$cache_path.'/services.json" ne contient aucun service Zabbix.';
    header('Location: '.URL.'/index.php/services/zabbix');
    exit(0);
}

$zabbix = array();
foreach (Service::get_records(array('plugin' => PLUGIN_ZABBIX)) as $record) {
    $zabbix[$record->name] = $record;
}

$services = array();
foreach ($cache as $data) {
    if (isset($zabbix[$data['name']]) === true) {
        continue;
    }

    $services[] = $data['name'];
}
sort($services);

if (isset($_POST['pattern']) === false) {
    $_POST['pattern'] = '';
}
$_POST['pattern'] = trim($_POST['pattern']);

$results = array();
if (empty($_POST['pattern']) === false) {
    $regexp = str_replace('/', '\/', $_POST['pattern']);
    foreach ($cache as $data) {
        if (preg_match('/'.$regexp.'/i', $data['name']) !== 1) {
            continue;
        }

        if (isset($zabbix[$data['name']]) === true) {
            continue;
        }

        $results[] = $data['name'];
    }

    if (isset($results[0]) === false) {
        $_POST['errors'][] = 'Aucun service ne correspond à la recherche "'.htmlentities($_POST['pattern']).'".';
    }
}

if (isset($_POST['submit']) === true) {
    if (is_array($_POST['services']) === true) {
        // On ajoute un ou plusieurs services...
        foreach ($_POST['services'] as $service_name) {
            $key = array_search($service_name, $results);

            if ($key === false) {
                $_SESSION['messages']['warnings'][] = 'Le service "'.htmlentities($service_name).'" n\'a pas été ajouté.';
                continue;
            }

            $service = new Service();
            $service->name = $service_name;
            $service->idplugin = PLUGIN_ZABBIX;

            $_POST['errors'] = $service->check_data();
            if (isset($_POST['errors'][0]) === true) {
                break;
            }

            $_POST = array_merge($_POST, $service->save());

            if (isset($_POST['errors'][0]) === true) {
                break;
            }

            unset($results[$key]);
        }
    } elseif (empty($service->id) === false) {
        $service->name = $_POST['services'];

        $_POST['errors'] = $service->check_data();
        if (isset($_POST['errors'][0]) === false) {
            $_POST = array_merge($_POST, $service->save());
        }
    } else {
        $_POST['errors'][0] = 'Une erreur est survenue lors de l\'enregistrement des données.';
    }

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        // On force la mise à jour des groupements de service Isou.
        require PRIVATE_PATH.'/plugins/monitoring/isou/lib.php';
        plugin_isou_update_grouping();

        header('Location: '.URL.'/index.php/services/zabbix');
        exit(0);
    }
}

sort($results);
$smarty->assign('results', $results);

$smarty->assign('service', $service);
$smarty->assign('services', $services);

$SUBTEMPLATE = 'edit.tpl';
