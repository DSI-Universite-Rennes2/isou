<?php

use UniversiteRennes2\Isou\Category;

if (isset($PAGE_NAME[2]) === true && ctype_digit($PAGE_NAME[2]) === true) {
    $category = Category::get_record(array('id' => $PAGE_NAME[2]));

    if ($category !== false) {
        if ($PAGE_NAME[1] === 'down') {
            $_SESSION['messages'] = $category->move_down();
        } else {
            $_SESSION['messages'] = $category->move_up();
        }
    }
}
