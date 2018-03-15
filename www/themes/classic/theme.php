<?php

use Isou\Helpers\Style;

$theme_version = '1';

$STYLES[] = new Style(URL.'/themes/classic/styles/common.css?v='.$theme_version);
$STYLES[] = new Style(URL.'/themes/classic/styles/menu.css?v='.$theme_version);

switch ($PAGE_NAME[0]) {
    // public
    case 'actualite':
        $STYLES[] = new Style(URL.'/themes/classic/styles/news.css?v='.$theme_version);
        break;
    case 'liste':
        $STYLES[] = new Style(URL.'/themes/classic/styles/table.css?v='.$theme_version);
        $STYLES[] = new Style(URL.'/themes/classic/styles/contact.css?v='.$theme_version);
        break;
    case 'tableau':
        $STYLES[] = new Style(URL.'/themes/classic/styles/board.css?v='.$theme_version);
        break;
    case 'journal':
        $STYLES[] = new Style(URL.'/themes/classic/styles/record.css?v='.$theme_version);
        break;
    case 'calendrier':
        $STYLES[] = new Style(URL.'/themes/classic/styles/calendar.css?v='.$theme_version);
        $STYLES[] = new Style(URL.'/themes/classic/styles/news.css?v='.$theme_version);
        break;
    case 'contact':
        $STYLES[] = new Style(URL.'/themes/classic/styles/contact.css?v='.$theme_version);
        break;
    case 'rss':
        $STYLES[] = new Style(URL.'/themes/classic/styles/rss_config.css?v='.$theme_version);
        break;
    // private
    case 'evenements':
        $STYLES[] = new Style(URL.'/themes/classic/styles/events.css?v='.$theme_version);
        break;
    case 'annonce':
        $STYLES[] = new Style(URL.'/themes/classic/styles/annonce.css?v='.$theme_version);
        break;
    case 'statistiques':
        $STYLES[] = new Style(URL.'/themes/classic/styles/stats.css?v='.$theme_version);
        $STYLES[] = new Style(URL.'/themes/classic/styles/common.css?v='.$theme_version, 'print');
        $STYLES[] = new Style(URL.'/themes/classic/styles/stats_print.css?v='.$theme_version, 'print');
        break;
    case 'services':
        $STYLES[] = new Style(URL.'/themes/classic/styles/services.css?v='.$theme_version);
        break;
    case 'dependances':
        $STYLES[] = new Style(URL.'/themes/classic/styles/dependencies.css?v='.$theme_version);
        break;
    case 'categories':
        $STYLES[] = new Style(URL.'/themes/classic/styles/categories.css?v='.$theme_version);
        break;
    case 'configuration':
        $STYLES[] = new Style(URL.'/themes/classic/styles/configuration.css?v='.$theme_version);
        break;
    case 'aide':
        $STYLES[] = new Style(URL.'/themes/classic/styles/help.css?v='.$theme_version);
        break;
}
