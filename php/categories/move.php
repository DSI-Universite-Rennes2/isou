<?php

if (isset($PAGE_NAME[2]) === true && ctype_digit($PAGE_NAME[2]) === true) {
    $category = get_category(array('id' => $PAGE_NAME[2]));

    if ($category !== false) {
        if ($PAGE_NAME[1] === 'down') {
            $_SESSION['messages'] = $category->down();
        } else {
            $_SESSION['messages'] = $category->up();
        }
    }
}
