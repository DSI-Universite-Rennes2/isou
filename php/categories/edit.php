<?php

use UniversiteRennes2\Isou;

if (isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])) {
    $category = get_category($PAGE_NAME[2]);
} else {
    $category = false;
}

if ($category === false) {
    $category = new UniversiteRennes2\Isou\Category();
}

if (isset($_POST['name'])) {
    $category->name = $_POST['name'];
    $_POST['errors'] = $category->check_data();

    if (!isset($_POST['errors'][0])) {
        $_POST = array_merge($_POST, $category->save());
        if (!isset($_POST['errors'][0])) {
            $_SESSION['messages']['successes'] = $_POST['successes'];

            header('Location: '.URL.'/index.php/categories');
            exit(0);
        }
    }
}

$smarty->assign('category', $category);

$TEMPLATE = 'categories/edit.tpl';
