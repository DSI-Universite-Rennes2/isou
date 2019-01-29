<?php

use Isou\Helpers\SimpleMenu;

$TITLE .= ' - Administration des évènements';

$submenu = array();
$submenu['prevus'] = new SimpleMenu('interruptions prévues', '', URL.'/index.php/evenements/prevus');
$submenu['imprevus'] = new SimpleMenu('interruptions non prévues', '', URL.'/index.php/evenements/imprevus');
$submenu['reguliers'] = new SimpleMenu('interruptions régulières', '', URL.'/index.php/evenements/reguliers');
$submenu['fermes'] = new SimpleMenu('services fermés', '', URL.'/index.php/evenements/fermes');
$submenu['autres'] = new SimpleMenu('autres interruptions', '', URL.'/index.php/evenements/autres');

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
    case 'more':
        require_once PRIVATE_PATH.'/php/events/more.php';
        break;
    case 'list':
    default:
        require_once PRIVATE_PATH.'/php/events/list.php';
}

$smarty->assign('show_add_button', ($PAGE_NAME[1] !== 'autres' && $PAGE_NAME[2] === 'list'));
$smarty->assign('eventtype', $PAGE_NAME[1]);
$smarty->assign('submenu', $submenu);
$smarty->assign('subtemplate', $subtemplate);

$TEMPLATE = 'events/index.tpl';
