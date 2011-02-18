<?php

// menu sur lequel isou pointe par défaut
// utiliser l'index du tableau associatif $MENU
$DEFAULTMENU = 'actualite';

/*
 * définit les "vues" disponibles aux utilisateurs lambda
 * nb : commenter les entrées que vous ne souhaitez pas voir apparaitre
 */

$page = new stdClass();
$page->model = '/php/public_news.php';
$page->template = 'public_news';
$page->public = TRUE;
$MENU['actualite'] = $page;

$page = new stdClass();
$page->model = '/php/public_table.php';
$page->template = 'public_table';
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
$page->model = '/php/public_journal.php';
$page->template = 'public_journal';
$page->public = TRUE;
$MENU['journal'] = $page;

$page = new stdClass();
$page->model = '/php/public_calendar.php';
$page->template = 'public_calendar';
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
$page->model = '/php/public_rss_config.php';
$page->template = 'public_rss_config';
$page->public = TRUE;
$MENU['rss/config'] = $page;


/*
 * menu pour administrateur, ne rien commenter
 */

$page = new stdClass();
$page->model = '/php/private_events.php';
$page->template = 'private_events_view';
$page->public = FALSE;
$MENU['evenements'] = $page;

$page = new stdClass();
$page->model = '/php/private_annonce.php';
$page->template = 'private_annonce';
$page->public = FALSE;
$MENU['annonce'] = $page;

$page = new stdClass();
$page->model = '/php/private_statistics.php';
$page->template = 'private_statistics';
$page->public = FALSE;
$MENU['statistiques'] = $page;

$page = new stdClass();
$page->model = '/php/private_services.php';
$page->template = 'private_services';
$page->public = FALSE;
$MENU['services'] = $page;

$page = new stdClass();
$page->model = '/php/private_dependencies.php';
$page->template = 'private_dependencies';
$page->public = FALSE;
$MENU['dependances'] = $page;

$page = new stdClass();
$page->model = '/php/private_categories.php';
$page->template = 'private_categories';
$page->public = FALSE;
$MENU['categories'] = $page;

$page = new stdClass();
$page->model = '/php/private_help.php';
$page->template = 'private_help';
$page->public = FALSE;
$MENU['aide'] = $page;

?>
