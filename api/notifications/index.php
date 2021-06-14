<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

if ($CFG['notifications_enabled'] === '0') {
    exit(0);
}

if (isset($PAGE_NAME[1]) === false) {
    $PAGE_NAME[1] = '';
}

switch ($PAGE_NAME[1]) {
    case 'enregistrement':
        require PRIVATE_PATH.'/api/notifications/subscribe.php';
        break;
    case 'desinscription':
        require PRIVATE_PATH.'/api/notifications/unsubscribe.php';
        break;
    case 'cle-publique-serveur':
        require PRIVATE_PATH.'/api/notifications/public_key_server.php';
        break;
}
