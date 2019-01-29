<?php

use League\CommonMark\CommonMarkConverter;

$TITLE .= ' - Aide';

$helps = array();
$helps['configuration'] = (object) ['active' => false, 'label' => 'Configuration', 'file' => 'configuration.md'];
$helps['categories'] = (object) ['active' => false, 'label' => 'Catégories', 'file' => 'categories.md'];
$helps['services'] = (object) ['active' => false, 'label' => 'Services', 'file' => 'services.md'];
$helps['dependances'] = (object) ['active' => false, 'label' => 'Dépendances', 'file' => 'dependancies.md'];
$helps['evenements'] = (object) ['active' => false, 'label' => 'Évènements', 'file' => 'events.md'];
$helps['statistiques'] = (object) ['active' => false, 'label' => 'Statistiques', 'file' => 'statistics.md'];
$helps['vues-publiques'] = (object) ['active' => false, 'label' => 'Vues publiques', 'file' => 'views.md'];

if (isset($PAGE_NAME[1], $helps[$PAGE_NAME[1]]) === false) {
    $PAGE_NAME[1] = 'configuration';
}

$helps[$PAGE_NAME[1]]->active = true;

$content = file_get_contents(PRIVATE_PATH.'/markdown/help/'.$helps[$PAGE_NAME[1]]->file);

$converter = new CommonMarkConverter();
$help = $converter->convertToHtml($content);

$smarty->assign('help', $help);
$smarty->assign('helps', $helps);

$TEMPLATE = 'help/index.tpl';
