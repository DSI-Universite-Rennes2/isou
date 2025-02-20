<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/isou/html');

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = '';
}

switch ($PAGE_NAME[2]) {
    case 'edit':
        require PRIVATE_PATH.'/plugins/monitoring/isou/php/edit.php';
        break;
    case 'delete':
        require PRIVATE_PATH.'/plugins/monitoring/isou/php/delete.php';
        break;
    case 'inspect':
        require PRIVATE_PATH.'/plugins/monitoring/isou/php/inspect.php';
        break;
    case 'grouping':
        require PRIVATE_PATH.'/plugins/monitoring/isou/php/grouping.php';
        break;
    default:
        require PRIVATE_PATH.'/plugins/monitoring/isou/php/list.php';
}
