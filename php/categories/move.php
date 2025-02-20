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

    if ($category !== false) {
        if ($PAGE_NAME[1] === 'down') {
            $_SESSION['messages'] = $category->move_down();
        } else {
            $_SESSION['messages'] = $category->move_up();
        }
    }
}
