<?php

$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/classic/common.css');
$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/classic/menu.css');

switch($PAGE_NAME[0]){
	// public
	case 'actualite':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/news.css');
		break;
	case 'liste':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/table.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/contact.css');
		break;
	case 'tableau':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/board.css');
		break;
	case 'journal':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/record.css');
		break;
	case 'calendrier':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/calendar.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/news.css');
		break;
	case 'contact':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/contact.css');
		break;
	case 'rss':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/rss_config.css');
		break;
	// private
	case 'evenements':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/events.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/private_events_calendar.css', 'all');
		break;
	case 'annonce':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/annonce.css');
		break;
	case 'statistiques':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/stats.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/common.css', 'print');
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/stats_print.css', 'print');
		if(!isset($_GET['history'])){
			$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/visualize.jQuery.css');
			$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/stats_graphic.css');
		}
		break;
	case 'services':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/services.css');
		break;
	case 'dependances':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/dependencies.css');
		break;
	case 'categories':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/categories.css');
		break;
	case 'configuration':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/configuration.css');
		break;
	case 'aide':
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/help.css');
		break;
}


