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
    case 'autres':
        unset($options['plugin']);
        $options['notplugin'] = PLUGIN_ISOU;
        $subtemplate = 'events/list_plugins_events.tpl';

        $plugins_ = array();
        foreach ($plugins as $plugin) {
            $plugins_[$plugin->id] = $plugin;
        }
        $smarty->assign('plugins', $plugins_);
        break;
    case 'fermes':
        $options['since'] = new DateTime(strftime('%FT%T', mktime(0, 0, 0) - 35 * 24 * 60 * 60));
        $options['type'] = Event::TYPE_CLOSED;
        $subtemplate = 'events/list_closed_events.tpl';
        break;
    case 'imprevus':
        $options['since'] = new DateTime(strftime('%FT%T', mktime(0, 0, 0) - 35 * 24 * 60 * 60));
        $options['type'] = Event::TYPE_UNSCHEDULED;
        $subtemplate = 'events/list_default_events.tpl';
        break;
    case 'reguliers':
        $options['type'] = Event::TYPE_REGULAR;
        $options['sort'] = array('s.name', 'e.startdate', 'e.enddate');
        $subtemplate = 'events/list_regular_events.tpl';
        break;
    case 'prevus':
    default:
        $options['since'] = new DateTime(strftime('%FT%T', mktime(0, 0, 0) - 35 * 24 * 60 * 60));
        $options['type'] = Event::TYPE_SCHEDULED;
        $subtemplate = 'events/list_default_events.tpl';
}

$smarty->assign('events', Event::get_records($options));
