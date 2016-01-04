<?php

$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/bootstrap.min.css');
$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/common.css');

switch($current_page->url){
	case 'actualite':
		$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/news.css');
		break;
	case 'calendrier':
		$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/calendar.css');
		break;
	case 'dependances':
		$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/dependencies.css');
		break;
	case 'rss':
		$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/rss.css');
}

