<?php

require_once PRIVATE_PATH.'/libs/dependencies.php';
require_once PRIVATE_PATH.'/libs/services.php';

$TITLE .= ' - Administration des dépendances';

if (!isset($PAGE_NAME[1])) {
    $PAGE_NAME[1] = '';
}

switch ($PAGE_NAME[1]) {
    case 'service':
        require PRIVATE_PATH.'/php/dependencies/groups/index.php';
        break;
    default:
        require PRIVATE_PATH.'/php/dependencies/services/list.php';
}
/*

    $action = (isset($PAGE_NAME[3]) && in_array($PAGE_NAME[3], array('edit', 'delete', 'duplicate')));
    $group = (isset($PAGE_NAME[5]) && $PAGE_NAME[4] === 'group' && ctype_digit($PAGE_NAME[5]));
    if($group){
        $group = get_dependency_group($group);
    }

    $content = (isset($PAGE_NAME[7]) && $PAGE_NAME[6] === 'content' && ctype_digit($PAGE_NAME[7]));

    if(isset($PAGE_NAME[3]

    if(!isset($PAGE_NAME[3])){
        $PAGE_NAME[3] = 'view';
    }

    switch($PAGE_NAME[3]){
        case 'edit':
            require PRIVATE_PATH.'/php/dependencies/edit.php';
            break;
        case 'delete':
            require PRIVATE_PATH.'/php/dependencies/delete.php';
            break;
        case 'duplicate':
            require PRIVATE_PATH.'/php/dependencies/duplicate.php';
            break;
        default:
            require PRIVATE_PATH.'/php/dependencies/view.php';
    }
}
*/
