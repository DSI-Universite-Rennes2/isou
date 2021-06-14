<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Service;

if (isset($PAGE_NAME[3]) === true && ctype_digit($PAGE_NAME[3]) === true) {
    $service = Service::get_record(array('id' => $PAGE_NAME[3], 'plugin' => PLUGIN_ISOU));
} else {
    $service = false;
}

if ($service === false) {
    $service = new Service();
    $service->idplugin = PLUGIN_ISOU;
}

if (isset($_POST['category'], $_POST['name'], $_POST['url'], $_POST['visible'], $_POST['locked'], $_POST['state']) === true) {
    $service->idcategory = $_POST['category'];
    $service->name = $_POST['name'];
    $service->url = $_POST['url'];
    $service->visible = $_POST['visible'];
    $service->locked = $_POST['locked'];
    $service->state = $_POST['state'];

    $_POST['errors'] = $service->check_data();
    if (isset($_POST['errors'][0]) === false) {
        $_POST = array_merge($_POST, $service->save());
        if (isset($_POST['errors'][0]) === false) {
            $_SESSION['messages']['successes'] = $_POST['successes'];

            header('Location: '.URL.'/index.php/services/isou');
            exit(0);
        }
    }
}

$smarty->assign('options_visible', array('1' => 'Afficher', '0' => 'Masquer'));
$smarty->assign('options_locked', array('1' => 'Verrouiller', '0' => 'Déverrouiller'));

$options_state = array();
foreach ($STATES as $state) {
    $options_state[$state->id] = $state->title;
}
$smarty->assign('options_state', $options_state);

$smarty->assign('service', $service);

$smarty->assign('categories', Category::get_records(array('fetch_column' => true)));

$SUBTEMPLATE = 'edit.tpl';
