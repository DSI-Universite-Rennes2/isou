<?php

function get_menus($options = array()) {
    global $DB;

    $joins = array();
    $params = array();
    $conditions = array();

    if (isset($options['id']) === true) {
        if (ctype_digit($options['id']) === true) {
            $conditions[] = 'm.id = ?';
            $params[] = $options['id'];
        } else {
            $LOGGER->addInfo('L\'option \'id\' doit être un entier.', array('value', $options['id']));
        }
    }

    if (isset($options['type']) === true) {
        if (is_string($options['type']) === false) {
            $LOGGER->addInfo('L\'option \'type\' doit être un booléan.', array('value', $options['type']));
        } else {
            $params['type'] = $options['type'];
            $conditions[] = 'm.type = :type';
        }
    }

    if (isset($options['active']) === true) {
        if (is_bool($options['active']) === false) {
            $LOGGER->addInfo('L\'option \'active\' doit être un booléan.', array('value', $options['active']));
        } else if ($options['active'] === true) {
            $conditions[] = 'm.active = 1';
        }
    }

    if (isset($conditions[0]) === true) {
        $sql_conditions = ' WHERE '.implode(' AND ', $conditions);
    } else {
        $sql_conditions = '';
    }

    $sql = "SELECT m.id, m.label, m.title, m.url, m.model, m.type, m.position, m.active".
        " FROM menus m".
        implode(' ', $joins).
        $sql_conditions.
        " ORDER BY m.position";
    $query = $DB->prepare($sql);
    $query->execute($params);

    $query->setFetchMode(PDO::FETCH_CLASS, 'Isou\Helpers\Menu');
    $records = $query->fetchAll();

    if (isset($options['one_record']) === true) {
        if (isset($records[0]) === true) {
            return $records[0];
        } else {
            return false;
        }
    }

    $menus = array();
    foreach ($records as $i => $record) {
        $menus[$record->url] = $record;
    }

    return $menus;
}

function get_menu_sorted_by_url() {
    global $DB;

    $menu = array();

    $sql = "SELECT url, label".
            " FROM menus".
            " WHERE type = 'guest'".
            " ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

function get_active_menu() {
    return get_menus(array('active' => true, 'type' => 'guest'));
}

function get_active_menu_sorted_by_url() {
    global $DB;

    $menu = array();

    $sql = "SELECT url, label".
            " FROM menus".
            " WHERE active=1".
            " ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

function get_administration_menu() {
    return get_menus(array('type' => 'admin'));
}
