<?php

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
    default:
        require PRIVATE_PATH.'/plugins/monitoring/isou/php/list.php';
}
