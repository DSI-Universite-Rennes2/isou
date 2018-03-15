<?php

use UniversiteRennes2\Isou\Service;

if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = get_service(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_SHINKEN));
} else {
    $service = false;
}

if ($service === false) {
    $service = new Service();
    $service->idplugin = PLUGIN_SHINKEN;
}

// Check cache.
$cache_path = PRIVATE_PATH.'/cache/plugins/shinken';
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

$services = array();
foreach ($cache as $data) {
    $services[] = $data['name'];
}
sort($services);

if (isset($_POST['service']) === true) {
    // Vérifie que le service existe.
    if (in_array($_POST['service'], $services, true) === false) {
        $_POST['errors'][] = 'Le service "'.$_POST['service'].'" n\'existe pas.';
    }

    if (isset($_POST['errors'][0]) === false) {
        $service->name = $_POST['service'];

        $_POST = array_merge($_POST, $service->save());
        if (isset($_POST['errors'][0]) === false) {
            $_SESSION['messages']['successes'] = $_POST['successes'];

            header('Location: '.URL.'/index.php/services/shinken');
            exit(0);
        }
    }
}

$smarty->assign('service', $service);
$smarty->assign('services', $services);

$SUBTEMPLATE = 'edit.tpl';
