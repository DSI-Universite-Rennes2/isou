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

$theme_version = '1.1';

$STYLES[] = new Style('//unpkg.com/bootstrap@3.3/dist/css/bootstrap.min.css');
$STYLES[] = new Style(URL.'/themes/bootstrap3/css/common.css?v='.$theme_version);

if (preg_match('#^dependances/service/[0-9]+/group/[0-9]+/content/edit/0$#', implode('/', $PAGE_NAME)) === 1) {
    $SCRIPTS[] = new Script(URL.'/scripts/dependencies.js');
}

if ($PAGE_NAME[0] === 'annonce') {
    $SCRIPTS[] = new Script(URL.'/scripts/tinymce/tinymce.min.js');
    $SCRIPTS[] = new Script(URL.'/scripts/announcement.js');
}
