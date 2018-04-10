<?php

use Isou\Helpers\SimpleMenu;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/events_descriptions.php';
require_once PRIVATE_PATH.'/libs/services.php';

$TITLE .= ' - Administration des évènements';

$submenu = array();
$submenu['prevus'] = new SimpleMenu('interruptions prévues', '', URL.'/index.php/evenements/prevus');
$submenu['imprevus'] = new SimpleMenu('interruptions non prévues', '', URL.'/index.php/evenements/imprevus');
$submenu['reguliers'] = new SimpleMenu('interruptions régulières', '', URL.'/index.php/evenements/reguliers');
$submenu['fermes'] = new SimpleMenu('service fermé', '', URL.'/index.php/evenements/fermes');

if (isset($PAGE_NAME[1]) === false || isset($submenu[$PAGE_NAME[1]]) === false) {
    $PAGE_NAME[1] = 'prevus';
}
$submenu[$PAGE_NAME[1]]->selected = true;

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = 'list';
}

switch ($PAGE_NAME[2]) {
    case 'delete':
        require_once PRIVATE_PATH.'/php/events/delete.php';
        break;
    case 'edit':
        require_once PRIVATE_PATH.'/php/events/edit.php';
        break;
    case 'list':
    default:
        require_once PRIVATE_PATH.'/php/events/list.php';
}

$smarty->assign('eventtype', $PAGE_NAME[1]);
$smarty->assign('submenu', $submenu);
$smarty->assign('subtemplate', $subtemplate);

$TEMPLATE = 'events/index.tpl';
