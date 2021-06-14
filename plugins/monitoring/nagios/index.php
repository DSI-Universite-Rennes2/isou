<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Plugin;

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/nagios/html');

$plugin = Plugin::get_record(array('id' => PLUGIN_NAGIOS));

if (empty($plugin->active) === true) {
    $PAGE_NAME[2] = '';
}

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = '';
}

switch ($PAGE_NAME[2]) {
    case 'edit':
        require PRIVATE_PATH.'/plugins/monitoring/nagios/php/edit.php';
        break;
    case 'delete':
        require PRIVATE_PATH.'/plugins/monitoring/nagios/php/delete.php';
        break;
    default:
        require PRIVATE_PATH.'/plugins/monitoring/nagios/php/list.php';
}

$smarty->assign('plugin', $plugin);
