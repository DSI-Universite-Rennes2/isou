<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Category;

$categories = array();
$count_categories = 0;
foreach (Category::get_records() as $category) {
    $category->count_services = count($category->get_services());
    $categories[$category->id] = $category;
    $count_categories++;
}

$smarty->assign('categories', $categories);
$smarty->assign('count_categories', $count_categories);

$TEMPLATE = 'categories/list.tpl';
