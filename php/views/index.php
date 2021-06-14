<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Announcement;

// Charge les annonces.
$ANNOUNCEMENT = Announcement::get_record(array('empty' => false, 'visible' => true));

// Cherche la vue à charger.
foreach ($MENUS->public as $menu) {
    if ($menu->url !== $PAGE_NAME[0]) {
        continue;
    }

    $route = PRIVATE_PATH.'/plugins/view/'.$menu->path.'/php/view.php';
    if (is_readable($route) !== true) {
        continue;
    }

    $MENUS->public[$menu->path]->selected = true;
    require $route;
    break;
}

if (isset($TEMPLATE) === false && isset($CFG['menu_default']) === true) {
    // Charge la vue par défaut.
    $route = PRIVATE_PATH.'/plugins/view/'.$CFG['menu_default'].'/php/view.php';
    if (is_readable($route) === true) {
        $MENUS->public[$CFG['menu_default']]->selected = true;
        require $route;
    }
}

if (isset($TEMPLATE) === false) {
    // TODO: aucune vue dispo.
    $TEMPLATE = 'common/error_database.tpl';
}
