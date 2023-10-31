<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

if (DEV === true || (isset($USER->admin) === true && empty($USER->admin) === false)) {
    if (isset($PAGE_NAME[1]) === false) {
        $PAGE_NAME[1] = '';
    }

    switch ($PAGE_NAME[1]) {
        case 'description':
            require PRIVATE_PATH.'/api/events/description.php';
            break;
    }
}
