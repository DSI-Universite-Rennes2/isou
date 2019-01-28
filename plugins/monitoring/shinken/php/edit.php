<?php

use UniversiteRennes2\Isou\Service;

$service = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = Service::get_record(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_SHINKEN));
}

if ($service === false) {
    $service = new Service();
    $service->idplugin = PLUGIN_SHINKEN;
}

// Check cache.
$cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/shinken';
if (is_readable($cache_path.'/services.json') === false) {
    $_SESSION['messages']['errors'][] = 'Le fichier "'.$cache_path.'/services.json" n\'existe pas ou ne peut être lu.<br />'.
       'Assurez-vous que le cron s\'exécute correctement.';
    header('Location: '.URL.'/index.php/services/shinken');
    exit(0);
}

// Load cache.
$cache = file_get_contents($cache_path.'/services.json');
$cache = json_decode($cache, $array = true);

if ($cache === null) {
    $_SESSION['messages']['errors'][] = 'Le fichier "'.$cache_path.'/services.json" est corrompu.';
    header('Location: '.URL.'/index.php/services/shinken');
    exit(0);
}

if (count($cache) === 0) {
    $_SESSION['messages']['errors'][] = 'Le fichier "'.$cache_path.'/services.json" ne contient aucun service Shinken.';
    header('Location: '.URL.'/index.php/services/shinken');
    exit(0);
}

$shinken = array();
foreach (Service::get_records(array('plugin' => PLUGIN_SHINKEN)) as $record) {
    $shinken[$record->name] = $record;
}

$services = array();
foreach ($cache as $data) {
    if (isset($shinken[$data['name']]) === true) {
        continue;
    }

    $services[] = $data['name'];
}
sort($services);

$previews = array();
if (isset($_POST['service']) === true && empty($_POST['service']) === false) {
    if ($service->id === 0) {
        // On ajoute un ou des services... on active les regexp.
        $found = false;
        $regexp = str_replace('/', '\/', $_POST['service']);
        foreach ($cache as $data) {
            if (preg_match('/'.$regexp.'/i', $data['name']) === 1) {
                if (isset($shinken[$data['name']]) === true) {
                    continue;
                }

                $found = true;
                if (isset($_POST['preview']) === true) {
                    $previews[] = $data['name'];
                    continue;
                }

                $service->name = $data['name'];

                $_POST = array_merge($_POST, $service->save());

                if (isset($_POST['errors'][0]) === true) {
                    break;
                }

                $service->id = 0;
            }
        }

        $service->name = $_POST['service'];

        if ($found === false) {
            $_POST['errors'][] = 'Aucun service ne correspond à "'.$_POST['service'].'".';
        }
    } else {
        // On modifie un service. On veut un nom strict.
        if (in_array($_POST['service'], $services, true) === false) {
            $_POST['errors'][] = 'Le service "'.$_POST['service'].'" n\'existe pas.';
        }

        if (isset($_POST['errors'][0]) === false) {
            $service->name = $_POST['service'];

            $_POST = array_merge($_POST, $service->save());
        }
    }

    if (isset($_POST['errors'][0]) === false && isset($_POST['preview']) === false) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        header('Location: '.URL.'/index.php/services/shinken');
        exit(0);
    }
}

if (isset($_POST['preview']) === true) {
    $smarty->assign('previews', $previews);
}

$smarty->assign('service', $service);
$smarty->assign('services', $services);

$SUBTEMPLATE = 'edit.tpl';
