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

if (isset($_POST['title'], $_POST['message'], $_POST['startdate'], $_POST['starttime'], $_POST['enddate'], $_POST['endtime']) === true) {
    $announcement->title = $_POST['title'];
    $announcement->message = $_POST['message'];
    $announcement->startdate = $announcement->get_datetime($_POST['startdate'], $_POST['starttime']);
    $announcement->enddate = $announcement->get_datetime($_POST['enddate'], $_POST['endtime']);
    $announcement->author = sprintf('%s %s', $USER->firstname, $USER->lastname);
    $announcement->last_modification = new DateTime();

    $_POST['errors'] = $announcement->check_data();
    if (isset($_POST['errors'][0]) === false) {
        $_POST = array_merge($_POST, $announcement->save());
    }
} elseif ($announcement->startdate === null) {
    $announcement->startdate = new DateTime();
}

$smarty->assign('announcement', $announcement);

$subtemplate = 'announcement/html.tpl';
