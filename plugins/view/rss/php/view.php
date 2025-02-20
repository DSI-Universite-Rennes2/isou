<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Service;

if (count($MENUS->public) > 1) {
    $TITLE .= ' - Configuration Flux RSS';
}

$keys = array();

$categories = array();
foreach (Category::get_records(array('non-empty' => true)) as $category) {
    $category->services = array();
    $categories[$category->id] = $category;
}

$services = Service::get_records(array('plugin' => PLUGIN_ISOU, 'visible' => true));

foreach ($services as $service) {
    if ($service->enable === '0' || $service->visible === '0') {
        continue;
    }

    if (isset($categories[$service->idcategory]) === false) {
        $category = Category::get_record(array('id' => $service->idcategory));
        if ($category === false) {
            continue;
        }

        $categories[$service->idcategory] = $category;
        $categories[$service->idcategory]->services = array();
    }

    $categories[$service->idcategory]->services[] = $service;

    if (isset($_POST['keys'][$service->id]) === true) {
        $keys[] = $service->id;
    }
}

if (isset($_POST['generer']) === true) {
    if (empty($keys) === true) {
        $rss_url = URL.'/rss.php';
    } else {
        $rss_url = URL.'/rss.php?services='.rawurlencode(base64_encode(implode(',', $keys)));
    }
} else {
    $rss_url = null;
}

$smarty->assign('categories', $categories);
$smarty->assign('rss_url', $rss_url);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/rss/html');
$TEMPLATE = 'view.tpl';
