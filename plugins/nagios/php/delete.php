<?php

if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = get_service(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_NAGIOS));
} else {
    $service = false;
}

if ($service === false) {
    $_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

    header('Location: '.URL.'/index.php/services/'.$plugin->codename);
    exit(0);
}

if (isset($_POST['delete']) === true) {
    $_POST = array_merge($_POST, $service->delete());

    if (isset($_POST['successes'][0]) === true) {
        $_SESSION['messages'] = $_POST;

        header('Location: '.URL.'/index.php/services/'.$plugin->codename);
        exit(0);
    }
}

$smarty->assign('service', $service);

$SUBTEMPLATE = 'delete.tpl';
