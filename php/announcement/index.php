<?php

use UniversiteRennes2\Isou\Announcement;

$TITLE .= ' - Annonce';

$announcement = Announcement::get_record();

$options_visible = array(
    1 => 'Oui',
    0 => 'Non',
    );

if (isset($_POST['message'], $_POST['visible']) === true) {
    $announcement->message = $_POST['message'];
    $announcement->visible = $_POST['visible'];
    $announcement->author = $_SESSION['phpCAS']['user'];
    $announcement->last_modification = new DateTime();

    $_POST['errors'] = $announcement->check_data($options_visible);
    if (isset($_POST['errors'][0]) === false) {
        $_POST = array_merge($_POST, $announcement->save());
    }
}

$smarty->assign('options_visible', $options_visible);

$smarty->assign('announcement', $announcement);

$TEMPLATE = 'announcement/index.tpl';
