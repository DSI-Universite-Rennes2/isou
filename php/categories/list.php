<?php

$categories = array();
$count_categories = 0;
foreach(get_categories() as $category){
	$category->count_services = count($category->get_services());
	$categories[$category->id] = $category;
	$count_categories++;
}

$smarty->assign('categories', $categories);
$smarty->assign('count_categories', $count_categories);

$TEMPLATE = 'categories/list.tpl';

?>
