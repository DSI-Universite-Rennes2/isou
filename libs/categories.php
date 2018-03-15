<?php

function get_category($options = array()) {
    $options['one_record'] = true;

    return get_categories($options);
}

function get_categories($options = array()) {
    global $DB;

    $joins = array();
    $params = array();
    $conditions = array();

    if (isset($options['id']) === true) {
        if (ctype_digit($options['id']) === true) {
            $conditions[] = 'c.id = ?';
            $params[] = $options['id'];
        } else {
            $LOGGER->addInfo('L\'option \'id\' doit être un entier.', array('value', $options['id']));
        }
    }

    if (isset($options['non-empty']) === true) {
        if (is_bool($options['non-empty']) === false) {
            $LOGGER->addInfo('L\'option \'non-empty\' doit être un booléan.', array('value', $options['non-empty']));
        } else if ($options['non-empty'] === true) {
            $joins[] = ' JOIN services s ON c.id = s.idcategory';
        }
    }

    if (isset($conditions[0]) === true) {
        $sql_conditions = ' WHERE '.implode(' AND ', $conditions);
    } else {
        $sql_conditions = '';
    }

    $sql = "SELECT c.id, c.name, c.position".
        " FROM categories c".
        implode(' ', $joins).
        $sql_conditions.
        " ORDER BY c.position";
    $query = $DB->prepare($sql);
    $query->execute($params);

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Category');
    $categories = $query->fetchAll();

    if (isset($options['one_record']) === true) {
        if (isset($categories[0]) === true) {
            return $categories[0];
        } else {
            return false;
        }
    }

    return $categories;
}

function get_categories_sorted_by_id() {
    global $DB;

    $sql = "SELECT id, name FROM categories ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}
