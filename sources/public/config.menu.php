<?php

// menu sur lequel isou pointe par défaut
// utiliser l'index du tableau associatif $MENU
$DEFAULTMENU = 'actualite';

/*
 * définit les "vues" disponibles aux utilisateurs lambda
 * nb : commenter les entrées que vous ne souhaitez pas voir apparaitre
 */

$page = new stdClass();
$page->model = '/php/public/news.php';
$page->template = 'public/news';
$page->public = TRUE;
$MENU['actualite'] = $page;

$page = new stdClass();
$page->model = '/php/public/table.php';
$page->template = 'public/table';
$page->public = TRUE;
$MENU['liste'] = $page;

// EN TEST : problème de performance
/*
$page = new stdClass();
$page->model = '/php/public_board.php';
$page->template = 'public_board';
$page->public = TRUE;
$MENU['tableau'] = $page;
*/

$page = new stdClass();
$page->model = '/php/public/journal.php';
$page->template = 'public/journal';
$page->public = TRUE;
$MENU['journal'] = $page;

$page = new stdClass();
$page->model = '/php/public/calendar.php';
$page->template = 'public/calendar';
$page->public = TRUE;
$MENU['calendrier'] = $page;

/*
$page = new stdClass();
$page->model = '/php/public_contact.php';
$page->template = 'public_contact';
$page->public = TRUE;
$MENU['contact'] = $page;
*/

$page = new stdClass();
$page->model = '/php/public/rss.php';
$page->template = 'public/rss';
$page->public = TRUE;
$MENU['rss'] = $page;


/*
 * menu pour administrateur, ne rien commenter
 */

$page = new stdClass();
$page->model = '/php/events/events.php';
$page->template = 'events/view';
$page->public = FALSE;
$MENU['evenements'] = $page;

$page = new stdClass();
$page->model = '/php/announcement/announcement.php';
$page->template = 'announcement/announcement';
$page->public = FALSE;
$MENU['annonce'] = $page;

$page = new stdClass();
$page->model = '/php/statistics/statistics.php';
$page->template = 'statistics/statistics';
$page->public = FALSE;
$MENU['statistiques'] = $page;

$page = new stdClass();
$page->model = '/php/services/services.php';
$page->template = 'services/services';
$page->public = FALSE;
$MENU['services'] = $page;

$page = new stdClass();
$page->model = '/php/dependencies/dependencies.php';
$page->template = 'dependencies/dependencies';
$page->public = FALSE;
$MENU['dependances'] = $page;

$page = new stdClass();
$page->model = '/php/categories/categories.php';
$page->template = 'categories/categories';
$page->public = FALSE;
$MENU['categories'] = $page;

$page = new stdClass();
$page->model = '/php/configuration/configuration.php';
$page->template = 'configuration/configuration';
$page->public = FALSE;
$MENU['configuration'] = $page;

$page = new stdClass();
$page->model = '/php/help/help.php';
$page->template = 'help/help';
$page->public = FALSE;
$MENU['aide'] = $page;

?>
