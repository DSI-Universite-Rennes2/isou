<?php

use Isou\Helpers\Script;
use Isou\Helpers\Style;

$theme_version = '1';

$STYLES[] = new Style(URL.'/themes/bootstrap/css/bootstrap.min.css?v='.$theme_version);
$STYLES[] = new Style(URL.'/themes/bootstrap/css/common.css?v='.$theme_version);

if (isset($current_page->url) === true) {
    switch ($current_page->url) {
        case 'actualite':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/news.css?v='.$theme_version);
            break;
        case 'calendrier':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/calendar.css?v='.$theme_version);
            break;
        case 'dependances':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/dependencies.css?v='.$theme_version);
            break;
        case 'rss':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/rss.css?v='.$theme_version);
    }
}

if ($CFG['notifications_enabled'] === '1') {
    $SCRIPTS[] = new Script(URL.'/scripts/notifications.js');
}
