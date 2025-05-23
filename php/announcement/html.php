<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Announcement;

$announcement = Announcement::get_record();

$options_visible = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['message'], $_POST['visible']) === true) {
    $announcement->message = $_POST['message'];
    $announcement->visible = $_POST['visible'];
    $announcement->author = sprintf('%s %s', $USER->firstname, $USER->lastname);
    $announcement->last_modification = new DateTime();

    $_POST['errors'] = $announcement->check_data($options_visible);
    if (isset($_POST['errors'][0]) === false) {
        $_POST = array_merge($_POST, $announcement->save());
    }
}

$smarty->assign('options_visible', $options_visible);

$smarty->assign('announcement', $announcement);

$subtemplate = 'announcement/html.tpl';
