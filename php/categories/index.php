<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$TITLE .= ' - Administration des catégories';

if (isset($PAGE_NAME[1]) === false) {
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
