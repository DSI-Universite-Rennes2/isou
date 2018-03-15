<?php

$TITLE .= ' - Configuration Flux RSS';

require_once PRIVATE_PATH.'/libs/services.php';
require_once PRIVATE_PATH.'/libs/categories.php';

$key = 0;

$categories = array();
foreach (get_categories() as $category) {
    $category->services = array();
    $categories[$category->id] = $category;
}

$services = get_services(array('plugin' => PLUGIN_ISOU, 'visible' => true));

foreach ($services as $service) {
    if ($service->enable === '0' || $service->visible === '0') {
        continue;
    }

    if (!isset($categories[$service->idcategory])) {
        $category = get_category($service->idcategory);
        if ($category === false) {
            continue;
        }

        $categories[$service->idcategory] = $category;
        $categories[$service->idcategory]->services = array();
    }

    $categories[$service->idcategory]->services[] = $service;

    if (isset($_POST['keys'][$service->id])) {
        $key += pow(2, $service->rsskey);
    }
}

if (isset($_POST['generer'])) {
    if ($key === 0) {
        $rss_url = URL.'/rss.php';
    } else {
        $rss_url = URL.'/rss.php?key='.strtoupper(dechex($key));
    }
} else {
    $rss_url = null;
}

foreach ($categories as $idcategory => $category) {
    if (count($category->services) === 0) {
        unset($categories[$idcategory]);
    }
}

$smarty->assign('categories', $categories);
$smarty->assign('rss_url', $rss_url);

$TEMPLATE = 'public/rss_config.tpl';
