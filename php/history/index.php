<?php

use UniversiteRennes2\Isou\Event;

require_once PRIVATE_PATH.'/libs/events.php';
require_once PRIVATE_PATH.'/libs/services.php';

$TITLE .= ' - Historique des évènements';

if (isset($PAGE_NAME[2]) && ctype_digit($PAGE_NAME[2])) {
    $page = $PAGE_NAME[2];
} else {
    $page = 1;
}

if (isset($PAGE_NAME[4])) {
    $options_filter = explode(';', $PAGE_NAME[4]);
    if (count($options_filter) === 6) {
        $_POST['services'] = explode(',', $options_filter[0]);
        $_POST['event_type'] = $options_filter[1];
        $_POST['startdate'] = $options_filter[2];
        $_POST['enddate'] = $options_filter[3];
        $_POST['sort'] = $options_filter[4];
        $_POST['paging'] = $options_filter[5];
    }
}

// services
$options_services = get_isou_services_sorted_by_idtype();
$smarty->assign('options_services', $options_services);
$options_event_types = array(
    -1 => 'Tous',
    Event::TYPE_SCHEDULED => 'Prévues',
    Event::TYPE_UNSCHEDULED => 'Non prévues',
);
$smarty->assign('options_event_types', $options_event_types);

// sort
$options_sorts = array(
'Décroissant',
'Croissant',
);
$smarty->assign('options_sorts', $options_sorts);

// max result
$options_paging = array('-1' => 'illimité');
for ($i = 10; $i < 101; $i = $i + 10) {
    $options_paging[$i] = $i;
}
$smarty->assign('options_paging', $options_paging);

if (isset($_POST['services'], $_POST['event_type'], $_POST['startdate'], $_POST['enddate'], $_POST['sort'], $_POST['paging']) === true) {
    $events = array();

    $params = array();

    // services
    $sql_services = array();
    if (is_array($_POST['services'])) {
        foreach ($_POST['services'] as $service) {
            if (ctype_digit($service)) {
                $sql_services[] = $service;
            }
        }
    } else {
        $_POST['services'] = array();
    }

    if (isset($sql_services[0])) {
        $params = $sql_services;
        $sql_services = " AND s.id IN(?".str_repeat(',?', count($params) - 1).")";
    } else {
        $sql_services = '';
    }

    // event type
    if (!isset($options_event_types[$_POST['event_type']])) {
        $_POST['event_type'] = '-1';
    }

    if ($_POST['event_type'] === '-1') {
        $sql_events = " AND e.type < 2";
    } else {
        $sql_events = " AND e.type = ?";
        $params[] = $_POST['event_type'];
    }

    // startdate
    try {
        $startdate = new DateTime($_POST['startdate']);
        $_POST['startdate'] = $startdate->format('Y-m-d');
    } catch (Exception $exception) {
        $_POST['startdate'] = strftime('%Y-%m-01');
    }
    $sql_events .= " AND e.startdate >= ?";
    $params[] = $_POST['startdate'];

    // enddate
    if (empty($_POST['enddate']) === false) {
        try {
            $enddate = new DateTime($_POST['enddate']);
            $_POST['enddate'] = $enddate->format('Y-m-d');

            $sql_events .= " AND e.enddate <= ?";
            $params[] = $_POST['enddate'];
        } catch (Exception $exception) {
            $_POST['enddate'] = '';
        }
    }

    // sort
    if ($_POST['sort'] === '0') {
        $sql_sort = " ORDER BY e.startdate DESC";
    } else {
        $sql_sort = " ORDER BY e.startdate ASC";
    }

    // paging
    if ($_POST['paging'] !== '-1') {
        if (!isset($options_paging[$_POST['paging']])) {
            $_POST['paging'] = '10';
        }

        $sql_limit = " LIMIT ".$_POST['paging']." OFFSET ".(($page - 1) * $_POST['paging']);
    } else {
        $sql_limit = '';
    }


    $events = array();
    $sql = "SELECT s.name, e.startdate, e.enddate, ed.description, e.type".
        " FROM events e, events_descriptions ed, services s".
        " WHERE s.id = e.idservice".
        " AND ed.id = e.ideventdescription".
        $sql_services.
        $sql_events.
        " AND s.idplugin=?".
        $sql_sort;

    if (!isset($_POST['export'])) {
        $sql .= $sql_limit;

        $count_events = 0;
        $sql_count = "SELECT COUNT(e.id)".
            " FROM events e, events_descriptions ed, services s".
            " WHERE s.id = e.idservice".
            " AND ed.id = e.ideventdescription".
            $sql_services.
            $sql_events.
            " AND s.idplugin=?";
    }

    $params[] = PLUGIN_ISOU;

    $query = $DB->prepare($sql);
    $query->execute($params);
    foreach ($query->fetchAll(PDO::FETCH_OBJ) as $event) {
        try {
            $event->startdate = new DateTime($event->startdate);
            if ($event->enddate !== null) {
                $event->enddate = new DateTime($event->enddate);
                $diff = $event->startdate->diff($event->enddate);
                $event->total_minutes = round(($event->enddate->getTimestamp() - $event->startdate->getTimestamp()) / 60);
            } else {
                $diff = $event->startdate->diff(new DateTime());
                $event->total_minutes = round((TIME - $event->startdate->getTimestamp()) / 60);
            }

            list($days, $hours, $minutes) = explode(';', $diff->format('%a;%h;%i'));

            $event->total = array();

            if ($days === '1') {
                $event->total[] = '1 jour';
            } elseif ($days > 1) {
                $event->total[] = $days.' jours';
            }

            if ($hours === '1') {
                $event->total[] = '1 heure';
            } elseif ($hours > 1) {
                $event->total[] = $hours.' heures';
            }

            if ($minutes > 1) {
                $event->total[] = $minutes.' minutes';
            } else {
                $event->total[] = $minutes.' minute';
            }

            $event->total = implode(', ', $event->total);
        } catch (Exception $exception) {
            $LOGGER->addError($exception->getMessage());
            continue;
        }

        $events[] = $event;
    }

    if (!isset($_POST['export'])) {
        $query = $DB->prepare($sql_count);
        $query->execute($params);
        $count = $query->fetch(PDO::FETCH_NUM);
        $count_events += $count[0];
    }

    $smarty->assign('events', $events);

    if (isset($_POST['export'])) {
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=isou_export.csv');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Transfer-Encoding: binary');
        $smarty->display('history/export.tpl');
        exit(0);
    } else {
        $smarty->assign('count_events', $count_events);
    }

    // pagination
    if ($_POST['paging'] === '-1') {
        $count_pages = 1;
    } else {
        $count_pages = ceil($count_events / $_POST['paging']);
    }

    $options_filter = array();
    $options_filter[] = implode(',', $_POST['services']);
    $options_filter[] = $_POST['event_type'];
    $options_filter[] = $_POST['startdate'];
    $options_filter[] = $_POST['enddate'];
    $options_filter[] = $_POST['sort'];
    $options_filter[] = $_POST['paging'];
    $options_filter = implode(';', $options_filter);

    $pagination = array();
    for ($i = 1; $i <= $count_pages; $i++) {
        $selected = ($page == $i);
        $url = URL.'/index.php/statistiques/page/'.$i.'/filter/'.$options_filter.'#resultat';
        $pagination[] = new Isou\Helpers\SimpleMenu($i, 'Page '.$i, $url, $selected);
    }

    $smarty->assign('pagination', $pagination);
}

if (isset($_POST['startdate']) === false) {
    $_POST['startdate'] = strftime('%Y-01-01');
}

if (isset($_POST['enddate']) === false) {
    $_POST['enddate'] = '';
}

$TEMPLATE = 'history/index.tpl';
