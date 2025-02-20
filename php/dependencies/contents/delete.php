<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group_Content;
use UniversiteRennes2\Isou\Service;

$dependency_group_content = Dependency_Group_Content::get_record(array('id' => $PAGE_NAME[7]));

if ($dependency_group_content === false) {
    $_SESSION['messages'] = array('errors' => array('Ce contenu n\'existe pas.'));

    header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
    exit(0);
}

if (isset($_POST['delete']) === true) {
    $_POST = array_merge($_POST, $dependency_group_content->delete());

    if (isset($_POST['errors'][0]) === false) {
        $_SESSION['messages']['successes'] = $_POST['successes'];

        header('Location: '.URL.'/index.php/dependances/service/'.$service->id);
        exit(0);
    }
}

$smarty->assign('service', $service);
$smarty->assign('content', Service::get_record(array('id' => $dependency_group_content->idservice)));
$smarty->assign('dependency_group_content', $dependency_group_content);

$TEMPLATE = 'dependencies/contents/delete.tpl';
