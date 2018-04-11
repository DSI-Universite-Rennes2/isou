<?php

use UniversiteRennes2\Isou\Plugin;

function get_event($options = array()) {
    if (isset($options['id']) === false) {
        throw new Exception(__FUNCTION__.': le paramètre $options[\'id\'] est requis.');
    }

    $options['one_record'] = true;

    return get_events($options);
}

/**
  * @param array $options Array in format:
  *   after           => DateTime
  *   before          => DateTime
  *   idservice       => int
  *   one_record      => bool
  *   regular         => bool
  *   plugin          => int : index key from UniversiteRennes2\Isou\Service::$TYPES
  *   since           => DateTime
  *   finished        => bool
  *   state           => int : index key from UniversiteRennes2\Isou\State::$STATES
  *   tolerance       => int : seconds
  *   type            => int : index key from UniversiteRennes2\Isou\Event::$TYPES
  *   sort            => Array of strings
  *
  * @return array of UniversiteRennes2\Isou\Events
  */

function get_events($options = array()) {
    global $DB;

    $params = array();
    $conditions = array();

    $sql = "SELECT e.id, e.startdate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, e.idservice, s.name AS service_name".
            " FROM events e, events_descriptions ed, services s".
            " WHERE s.id=e.idservice".
            " AND ed.id=e.ideventdescription";

    if (isset($options['id'])) {
        if (ctype_digit($options['id'])) {
            $sql .= ' AND e.id = ?';
            $params[] = $options['id'];
        } else {
            $LOGGER->addInfo('L\'option \'id\' doit être un entier.', array('value', $options['id']));
        }
    }

    // after options
    if (isset($options['after']) && $options['after'] instanceof DateTime) {
        $sql .= " AND e.startdate >= ?";
        $params[] = $options['after']->format('Y-m-d\TH:i');
    }

    // before options
    if (isset($options['before']) && $options['before'] instanceof DateTime) {
        $sql .= " AND e.startdate < ?";
        $params[] = $options['before']->format('Y-m-d\TH:i');
    }

    // idservice options
    if (isset($options['idservice']) && ctype_digit($options['idservice'])) {
        $sql .= " AND s.id = ?";
        $params[] = $options['idservice'];
    }

    // regular options
    if (isset($options['regular'])) {
        if ($options['regular'] === true) {
            $sql .= " AND e.period IS NOT NULL";
        } else {
            $sql .= " AND e.period IS NULL";
        }
    }

    // plugin options
    if (isset($options['plugin']) === true && ctype_digit($options['plugin']) === true) {
        $sql .= " AND s.idplugin=?";
        $params[] = $options['plugin'];
    }

    // since options
    if (isset($options['since']) === true) {
        $sql .= " AND (e.enddate IS NULL OR e.startdate >= ?)";
        if ($options['since'] instanceof DateTime) {
            $params[] = $options['since']->format('Y-m-d\TH:i:s');
        } else {
            $params[] = $options['since'];
        }
    }

    // closed option
    if (isset($options['finished'])) {
        if ($options['finished'] === true) {
            $sql .= " AND e.enddate IS NOT NULL";
        } else {
            $sql .= " AND e.enddate IS NULL";
        }
    }

    // state options
    if (isset($options['state'], UniversiteRennes2\Isou\State::$STATES[$options['state']])) {
        $sql .= " AND e.state=?";
        $params[] = $options['state'];
    }

    // tolerance options
    if (isset($options['tolerance']) && ctype_digit($options['tolerance']) && $options['tolerance'] > 0) {
        $sql .= " AND".
            " (".
            " (e.enddate IS NULL)".// AND (strftime('%s', '".STR_TIME."') - strftime('%s', e.startdate)) > ".$options['tolerance'].")".
            " OR".
            " ((strftime('%s', e.enddate) - strftime('%s', e.startdate)) > ".$options['tolerance'].")".
            " )";
    }

    // type options
    if (isset($options['type'], UniversiteRennes2\Isou\Event::$TYPES[$options['type']])) {
        $sql .= " AND e.type=?";
        $params[] = $options['type'];
    }

    // sort options
    if (isset($options['sort']) && is_array($options['sort'])) {
        $sql .= " ORDER BY ".implode(', ', $options['sort']);
    } else {
        $sql .= " ORDER BY e.startdate, e.enddate";
    }

    $query = $DB->prepare($sql);
    $query->execute($params);

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');

    if (isset($options['one_record'])) {
        $events = $query->fetchAll();
        if (isset($events[0])) {
            return $events[0];
        } else {
            return false;
        }
    }

    return $query->fetchAll();
}


function get_events_by_type($since = null, $type = null, $servicetype = null, $tolerance = true) {
    global $CFG, $DB;

    $params = array();

    if ($type === null) {
        $sql_type = "";
    } else {
        $sql_type = " AND e.type = ?";
        $params[] = $type;
    }

    if ($servicetype === null) {
        $sql_servicetype = "";
    } else {
        $sql_servicetype = " AND s.idplugin = ?";
        $params[] = $servicetype;
    }

    if ($since === null) {
        $sql_tolerance = "";
    } else {
        $params[] = $since;
        if ($tolerance === true) {
            $plugin = Plugin::get_plugin(array('codename' => 'isou'));
            $sql_tolerance = " AND ((e.enddate > ? AND strftime('%s', enddate)-strftime('%s', startdate) > ?) OR e.enddate IS NULL)";
            $params[] = $plugin->settings->tolerance;
        } else {
            $sql_tolerance = " AND (e.enddate > ? OR e.enddate IS NULL)";
        }
    }

    $sql = "SELECT e.id, e.startdate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, s.id, s.name".
        " FROM events e, services s, events_descriptions ed".
        " WHERE s.id = e.idservice".
        " AND ed.id = e.ideventdescription".
        " AND s.enable = 1".$sql_type.$sql_servicetype.$sql_tolerance.
        " ORDER BY e.startdate DESC";
    $query = $DB->prepare($sql);
    $query->execute($params);

    return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');
}
