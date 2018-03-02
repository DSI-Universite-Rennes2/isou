<?php

$STYLES[] = new \Isou\Helpers\Style(URL.'/themes/classic/styles/common.css');
$STYLES[] = new \Isou\Helpers\Style(URL.'/themes/classic/styles/menu.css');

switch($PAGE_NAME[0]){
	// public
	case 'actualite':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/news.css');
		break;
	case 'liste':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/table.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/contact.css');
		break;
	case 'tableau':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/board.css');
		break;
	case 'journal':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/record.css');
		break;
	case 'calendrier':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/calendar.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/news.css');
		break;
	case 'contact':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/contact.css');
		break;
	case 'rss':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/rss_config.css');
		break;
	// private
	case 'evenements':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/events.css');
		break;
	case 'annonce':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/annonce.css');
		break;
	case 'statistiques':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/stats.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/common.css', 'print');
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/stats_print.css', 'print');
		break;
	case 'services':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/services.css');
		break;
	case 'dependances':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/dependencies.css');
		break;
	case 'categories':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/categories.css');
		break;
	case 'configuration':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/configuration.css');
		break;
	case 'aide':
		$STYLES[] = new Isou\Helpers\Style(URL.'/themes/classic/styles/help.css');
		break;
}
