<?php

use UniversiteRennes2\Isou\Category;
use UniversiteRennes2\Isou\Service;

$TITLE .= ' - Configuration Flux RSS';

$key = 0;

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
        $key += pow(2, $service->rsskey);
    }
}

if (isset($_POST['generer']) === true) {
    if ($key === 0) {
        $rss_url = URL.'/rss.php';
    } else {
        $rss_url = URL.'/rss.php?key='.strtoupper(dechex($key));
    }
} else {
    $rss_url = null;
}

$smarty->assign('categories', $categories);
$smarty->assign('rss_url', $rss_url);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/rss/html');
$TEMPLATE = 'view.tpl';
