<?php

use UniversiteRennes2\Isou\Dependency_Group;

$dependency_group = Dependency_Group::get_record(array('id' => $PAGE_NAME[5]));

if ($dependency_group === false) {
    $_SESSION['messages'] = array('errors' => array('Ce groupe n\'existe pas.'));

    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
}

if (isset($_POST['delete']) === true) {
    $_POST = array_merge($_POST, $dependency_group->delete());

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
        exit(0);
    }
}

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);

$TEMPLATE = 'dependencies/groups/delete.tpl';
