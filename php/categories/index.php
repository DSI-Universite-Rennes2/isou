<?php

require PRIVATE_PATH.'/libs/categories.php';

$TITLE .= ' - Administration des catégories';

if (!isset($PAGE_NAME[1])) {
    $PAGE_NAME[1] = '';
}

switch ($PAGE_NAME[1]) {
    case 'edit':
        require PRIVATE_PATH.'/php/categories/edit.php';
        break;
    case 'delete':
        require PRIVATE_PATH.'/php/categories/delete.php';
        break;
    case 'up':
    case 'down':
        require PRIVATE_PATH.'/php/categories/move.php';
        require PRIVATE_PATH.'/php/categories/list.php';
        break;
    default:
        require PRIVATE_PATH.'/php/categories/list.php';
}
