<?php

require PRIVATE_PATH.'/libs/announcements.php';

$TITLE .= ' - Annonce';

$announcement = get_announcement();

$options_visible = array(
1 => 'Oui',
0 => 'Non',
);

if (isset($_POST['message'], $_POST['visible'])) {
    $announcement->message = $_POST['message'];
    $announcement->visible = $_POST['visible'];
    $announcement->autor = $_SESSION['phpCAS']['user'];
    $announcement->last_modification = new DateTime();

    $_POST['errors'] = $announcement->check_data($options_visible);
    if (!isset($_POST['errors'][0])) {
        $_POST = array_merge($_POST, $announcement->save());
    }
}

$smarty->assign('options_visible', $options_visible);

$smarty->assign('announcement', $announcement);

$TEMPLATE = 'announcement/index.tpl';
