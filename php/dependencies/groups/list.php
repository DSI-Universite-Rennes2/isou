<?php

use UniversiteRennes2\Isou\Dependency_Group;

$smarty->assign('service', $service);
$smarty->assign('groups', Dependency_Group::get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($PAGE_NAME[2]));

$TEMPLATE = 'dependencies/groups/list.tpl';
