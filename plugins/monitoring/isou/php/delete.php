<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Service;

$service = false;
if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = Service::get_record(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_ISOU));
}

if ($service === false) {
    $_SESSION['messages'] = array('errors' => 'Ce service n\'existe pas.');

    header('Location: '.URL.'/index.php/services/isou');
    exit(0);
}

if (isset($_POST['delete']) === true) {
    $_POST = array_merge($_POST, $service->delete());

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages'] = $_POST;

        header('Location: '.URL.'/index.php/services/isou');
        exit(0);
    }
}

$smarty->assign('service', $service);

$SUBTEMPLATE = 'delete.tpl';
