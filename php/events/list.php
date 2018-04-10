<?php

use UniversiteRennes2\Isou\Event;

$options = array();
$options['plugin'] = PLUGIN_ISOU;
$options['sort'] = array(
    'e.enddate IS NULL DESC',
    'e.enddate DESC',
    'e.startdate DESC',
    );

switch ($PAGE_NAME[1]) {
    case 'fermes':
        $options['since'] = strftime('%FT%T', mktime(0, 0, 0) - 35 * 24 * 60 * 60);
        $options['type'] = Event::TYPE_CLOSED;
        $subtemplate = 'events/list_closed_events.tpl';
        break;
    case 'imprevus':
        $options['since'] = strftime('%FT%T', mktime(0, 0, 0) - 35 * 24 * 60 * 60);
        $options['type'] = Event::TYPE_UNSCHEDULED;
        $subtemplate = 'events/list_default_events.tpl';
        break;
    case 'reguliers':
        $options['type'] = Event::TYPE_REGULAR;
        $subtemplate = 'events/list_regular_events.tpl';
        break;
    case 'prevus':
    default:
        $options['since'] = strftime('%FT%T', mktime(0, 0, 0) - 35 * 24 * 60 * 60);
        $options['type'] = Event::TYPE_SCHEDULED;
        $subtemplate = 'events/list_default_events.tpl';
}

$smarty->assign('events', get_events($options));
$smarty->assign('services', get_services_sorted_by_id(PLUGIN_ISOU));
