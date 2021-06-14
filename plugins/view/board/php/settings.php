<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

require PRIVATE_PATH.'/php/views/settings.php';

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/view/board/html');
$VIEW_TEMPLATE = 'settings.tpl';
