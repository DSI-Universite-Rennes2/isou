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
    $service = Service::get_record(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_NAGIOS));
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

        // On force la mise à jour des groupements de service Isou.
        require PRIVATE_PATH.'/plugins/monitoring/isou/lib.php';
        plugin_isou_update_grouping();

        header('Location: '.URL.'/index.php/services/'.$plugin->codename);
        exit(0);
    }
}

$smarty->assign('service', $service);

$SUBTEMPLATE = 'delete.tpl';
