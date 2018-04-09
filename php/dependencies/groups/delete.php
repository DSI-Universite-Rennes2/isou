<?php

$dependency_group = get_dependency_group(array('id' => $PAGE_NAME[5]));

if ($dependency_group === false) {
    $_SESSION['messages'] = array('errors' => array('Ce groupe n\'existe pas.'));

    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
} elseif (isset($_POST['delete'])) {
    $_POST = array_merge($_POST, $dependency_group->delete());

    if (!isset($_POST['errors'][0])) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
        exit(0);
    }
}

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);

$TEMPLATE = 'dependencies/groups/delete.tpl';
