<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Event;

$options = array();
$options['plugin'] = PLUGIN_ISOU;
$options['has_category'] = true;
$options['sort'] = array(
    'e.enddate IS NULL DESC',
    'e.enddate DESC',
    'e.startdate DESC',
);

switch ($PAGE_NAME[1]) {
    case 'autres':
        unset($options['plugin']);
        $options['notplugin'] = PLUGIN_ISOU;
        $options['has_category'] = false;
        $subtemplate = 'events/list_plugins_events.tpl';

        $plugins_ = array();
        foreach ($plugins as $plugin) {
            $plugins_[$plugin->id] = $plugin;
        }
        $smarty->assign('plugins', $plugins_);
        break;
    case 'fermes':
        $options['since'] = new DateTime(date('Y-m-d\TH:i:s', mktime(0, 0, 0) - 35 * 24 * 60 * 60));
        $options['type'] = Event::TYPE_CLOSED;
        $subtemplate = 'events/list_closed_events.tpl';
        break;
    case 'reguliers':
        $options['type'] = Event::TYPE_REGULAR;
        $options['sort'] = array('s.name', 'e.startdate', 'e.enddate');
        $smarty->registerClass('Event', 'UniversiteRennes2\Isou\Event');
        $subtemplate = 'events/list_regular_events.tpl';
        break;
    case 'prevus':
        $options['since'] = new DateTime(date('Y-m-d\TH:i:s', mktime(0, 0, 0) - 35 * 24 * 60 * 60));
        $options['type'] = Event::TYPE_SCHEDULED;
        $subtemplate = 'events/list_default_events.tpl';
        break;
    case 'imprevus':
    default:
        $options['since'] = new DateTime(date('Y-m-d\TH:i:s', mktime(0, 0, 0) - 35 * 24 * 60 * 60));
        $options['type'] = Event::TYPE_UNSCHEDULED;
        $subtemplate = 'events/list_default_events.tpl';
}

$smarty->assign('events', Event::get_records($options));
