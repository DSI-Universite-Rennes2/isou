<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\SimpleMenu;

$TITLE .= ' - Annonce';

$submenu = array();
$submenu['annonce'] = new SimpleMenu('bandeau HTML', '', URL.'/index.php/annonce');

if ($CFG['notifications_enabled'] === '1') {
    $submenu['notification'] = new SimpleMenu('notification web', '', URL.'/index.php/annonce/notification');
}

if (isset($submenu['notification'], $PAGE_NAME[1]) === true && $PAGE_NAME[1] === 'notification') {
    $submenu['notification']->selected = true;
    require_once PRIVATE_PATH.'/php/announcement/webpush.php';
} else {
    $submenu['annonce']->selected = true;
    require_once PRIVATE_PATH.'/php/announcement/html.php';
}

$smarty->assign('submenu', $submenu);
$smarty->assign('subtemplate', $subtemplate);

$TEMPLATE = 'announcement/index.tpl';
