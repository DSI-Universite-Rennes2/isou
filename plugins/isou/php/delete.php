<?php

if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = get_service(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_ISOU));
} else {
    $service = false;
}

if ($service === false) {
    $_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

    header('Location: '.URL.'/index.php/services/isou');
    exit(0);
} elseif (isset($_POST['delete']) === true) {
    $_POST = array_merge($_POST, $service->delete());

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages'] = $_POST;

        header('Location: '.URL.'/index.php/services/isou');
        exit(0);
    }
}

$smarty->assign('service', $service);

$SUBTEMPLATE = 'delete.tpl';
