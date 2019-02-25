<?php

use Isou\Helpers\Style;

$theme_version = '1';

$STYLES[] = new Style('//unpkg.com/bootstrap@3.3/dist/css/bootstrap.min.css');
$STYLES[] = new Style(URL.'/themes/bootstrap3/css/common.css?v='.$theme_version);

if (isset($current_page->url) === true) {
    switch ($current_page->url) {
        case 'actualite':
            $STYLES[] = new Style(URL.'/themes/bootstrap3/css/news.css?v='.$theme_version);
            break;
        case 'calendrier':
            $STYLES[] = new Style(URL.'/themes/bootstrap3/css/calendar.css?v='.$theme_version);
            break;
        case 'dependances':
            $STYLES[] = new Style(URL.'/themes/bootstrap3/css/dependencies.css?v='.$theme_version);
            break;
        case 'rss':
            $STYLES[] = new Style(URL.'/themes/bootstrap3/css/rss.css?v='.$theme_version);
    }
}
