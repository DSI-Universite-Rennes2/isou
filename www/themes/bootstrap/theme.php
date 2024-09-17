<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\Script;
use Isou\Helpers\Style;

$theme_version = '4.0';

$STYLES[] = new Style('//unpkg.com/bootstrap@5.3/dist/css/bootstrap.min.css');
$STYLES[] = new Style('//unpkg.com/bootstrap-icons@1.11/font/bootstrap-icons.min.css');
$STYLES[] = new Style(URL.'/themes/bootstrap/css/common.css?v='.$theme_version);

if (preg_match('#^dependances/service/[0-9]+/group/[0-9]+/content/edit/0$#', implode('/', $PAGE_NAME)) === 1) {
    $SCRIPTS[] = new Script(URL.'/scripts/dependencies.js');
} elseif (preg_match('#^evenements/[a-z]+/edit/[0-9]+$#', implode('/', $PAGE_NAME)) === 1) {
    $SCRIPTS[] = new Script(URL.'/scripts/events.js');
} elseif ($PAGE_NAME[0] === 'annonce') {
    $SCRIPTS[] = new Script(URL.'/scripts/tinymce/tinymce.min.js');
    $SCRIPTS[] = new Script(URL.'/scripts/announcement.js?v=2');
}

$SCRIPTS[] = new Script('//unpkg.com/bootstrap@5.3/dist/js/bootstrap.bundle.min.js');
