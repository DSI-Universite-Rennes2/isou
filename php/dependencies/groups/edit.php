<?php

if (isset($PAGE_NAME[5]) && ctype_digit($PAGE_NAME[5])) {
    $dependency_group = get_dependency_group($PAGE_NAME[5]);
} else {
    $dependency_group = false;
}

if ($dependency_group === false) {
    $dependency_group = new UniversiteRennes2\Isou\Dependency_Group();
    $dependency_group->idservice = $service->id;
}

$options_redundants = array(
1 => 'Oui',
0 => 'Non',
);
$options_states = array(
1 => 'Orange',
2 => 'Rouge',
);
$options_services = array($service->id => $service->name);

if (isset($_POST['name'], $_POST['redundant'], $_POST['groupstate'], $_POST['message'])) {
    $dependency_group->name = $_POST['name'];
    $dependency_group->redundant = $_POST['redundant'];
    $dependency_group->groupstate = $_POST['groupstate'];
    $dependency_group->message = $_POST['message'];

    $_POST['errors'] = $dependency_group->check_data($options_redundants, $options_states, $options_services);

    if (!isset($_POST['errors'][0])) {
        $_POST = array_merge($_POST, $dependency_group->save());
        if (!isset($_POST['errors'][0])) {
            $_SESSION['messages'] = $_POST;

            header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
            exit(0);
        }
    }
}

$smarty->assign('options_states', $options_states);
$smarty->assign('options_redundants', $options_redundants);
$smarty->assign('options_services', $options_services);

$smarty->assign('service', $service);
$smarty->assign('dependency_group', $dependency_group);

$TEMPLATE = 'dependencies/groups/edit.tpl';
