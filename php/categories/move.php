<?php

if (isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])) {
    $category = get_category($PAGE_NAME[2]);

    if ($category !== false) {
        if ($PAGE_NAME[1] === 'down') {
            $_SESSION['messages'] = $category->down();
        } else {
            $_SESSION['messages'] = $category->up();
        }
    }
}
