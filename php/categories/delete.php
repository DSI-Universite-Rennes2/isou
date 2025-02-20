<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Category;

if (isset($PAGE_NAME[2]) === true && ctype_digit($PAGE_NAME[2]) === true) {
    $category = Category::get_record(array('id' => $PAGE_NAME[2]));
} else {
    $category = false;
}

if ($category === false) {
    $_SESSION['messages'] = array('errors' => array('Cette catégorie n\'existe pas.'));

    header('Location: '.URL.'/index.php/categories');
    exit(0);
} elseif (isset($_POST['delete']) === true) {
    $_POST = array_merge($_POST, $category->delete());

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        header('Location: '.URL.'/index.php/categories');
        exit(0);
    }
}

$smarty->assign('category', $category);

$TEMPLATE = 'categories/delete.tpl';
