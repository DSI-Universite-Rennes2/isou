<?php

$STYLES[] = new \Isou\Helpers\Style(URL.'/themes/bootstrap/css/bootstrap.min.css');
$STYLES[] = new \Isou\Helpers\Style(URL.'/themes/bootstrap/css/common.css');

if (isset($current_page->url) === true) {
    switch ($current_page->url) {
        case 'actualite':
            $STYLES[] = new \Isou\Helpers\Style(URL.'/themes/bootstrap/css/news.css');
            break;
        case 'calendrier':
            $STYLES[] = new \Isou\Helpers\Style(URL.'/themes/bootstrap/css/calendar.css');
            break;
        case 'dependances':
            $STYLES[] = new \Isou\Helpers\Style(URL.'/themes/bootstrap/css/dependencies.css');
            break;
        case 'rss':
            $STYLES[] = new \Isou\Helpers\Style(URL.'/themes/bootstrap/css/rss.css');
    }
}
