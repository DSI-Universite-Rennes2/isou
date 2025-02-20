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

$sql = "SELECT DISTINCT s.id, s.name, s.state".
    " FROM services s".
    " JOIN plugins p ON p.id = s.idplugin AND p.active = 1".
    " JOIN dependencies_groups_content dgc ON s.id = dgc.idservice".
    " JOIN dependencies_groups dg ON dg.id = dgc.idgroup".
    " WHERE dg.idservice = ?";
$query = $DB->prepare($sql);
$query->execute(array($service->id));
$service->dependencies = $query->fetchAll(PDO::FETCH_OBJ);

$smarty->assign('service', $service);

$SUBTEMPLATE = 'inspect.tpl';
