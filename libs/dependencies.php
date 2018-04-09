<?php

function get_dependency_group($options = array()) {
    if (isset($options['id']) === false) {
        throw new Exception('La fonction "get_dependency_group" doit passer en paramètre un tableau contenant un index "id".');
    }

    $options['one_record'] = true;

    return get_dependency_groups($options);
}

function get_dependency_groups($options = array()) {
    global $DB, $LOGGER;

    $params = array();
    $conditions = array();

    if (isset($options['id']) === true) {
        if (ctype_digit($options['id']) === true) {
            $conditions[] = 'dg.id = ?';
            $params[] = $options['id'];
        } else {
            $LOGGER->addInfo('L\'option \'id\' doit être un entier.', array('value', $options['id']));
        }
    }

    if (isset($options['service']) === true) {
        if (ctype_digit($options['service']) === true) {
            $conditions[] = 'dg.idservice = ?';
            $params[] = $options['service'];
        } else {
            $LOGGER->addInfo('L\'option \'service\' doit être un entier.', array('value', $options['service']));
        }
    }

    if (isset($conditions[0])) {
        $sql_condition = ' WHERE '.implode(' AND ', $conditions);
    } else {
        $sql_condition = '';
    }

    // get_service_dependency_group($id) SQL query.
    /*
    $sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, s.name AS service, dg.idmessage, dm.message".
            " FROM dependencies_groups dg, dependencies_messages dm, services s".
            " WHERE dm.id=dg.idmessage".
            " AND s.id=dg.idservice".
            " AND dg.id=?";
    */
    $sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message".
            " FROM dependencies_groups dg".
            " JOIN dependencies_messages dm ON dm.id = dg.idmessage".
            $sql_condition.
            " ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
    $query = $DB->prepare($sql);
    $query->execute($params);

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');

    if (isset($options['one_record']) === true) {
        $records = $query->fetchAll();
        if (isset($records[0]) === true) {
            return $records[0];
        } else {
            return false;
        }
    }

    return $query->fetchAll();
}

function get_service_reverse_dependency_groups($idservice, $state = null) {
    global $DB;

    $params = array($idservice);
    $sql_conditions = '';

    if ($state !== null) {
        $params[] = $state;
        $sql_conditions = " AND dgc.servicestate = ?";
    }

    $sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage".
        " FROM dependencies_groups dg, dependencies_groups_content dgc".
        " WHERE dg.id = dgc.idgroup".
        " AND dgc.idservice=?".
        $sql_conditions.
        " ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
    $query = $DB->prepare($sql);
    $query->execute($params);

    return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');
}

function get_dependency_groups_sorted_by_id() {
    global $DB, $FLAGS;

    $sql = "SELECT dg.id, dg.name, dg.groupstate".
        " FROM dependencies_groups dg".
        " WHERE dg.idservice=?".
        " ORDER BY dg.groupstate, UPPER(dg.name)";
    $query = $DB->prepare($sql);
    $query->execute(array($_GET['service']));

    $groups = array();
    while ($group = $query->fetch(PDO::FETCH_OBJ)) {
        $groups[$group->idgroup] = $group->name.' ('.$FLAGS[$group->groupstate]->title.')';
    }

    return $groups;
}

function get_dependency_group_content($options = array()) {
    if (isset($options['id']) === false) {
        throw new Exception('La fonction "get_dependency_group_content" doit passer en paramètre un tableau contenant un index "id".');
    }

    $options['one_record'] = true;

    return get_dependency_group_contents($options);
}

function get_dependency_group_contents($options = array()) {
    global $DB, $LOGGER;

    $params = array();
    $conditions = array();

    if (isset($options['id']) === true) {
        if (ctype_digit($options['id']) === true) {
            $conditions[] = 'dgc.id = ?';
            $params[] = $options['id'];
        } else {
            $LOGGER->addInfo('L\'option \'id\' doit être un entier.', array('value', $options['id']));
        }
    }

    if (isset($options['group']) === true) {
        if (ctype_digit($options['group']) === true) {
            $conditions[] = 'dgc.idgroup = ?';
            $params[] = $options['group'];
        } else {
            $LOGGER->addInfo('L\'option \'idgroup\' doit être un entier.', array('value', $options['group']));
        }
    }

    if (isset($conditions[0])) {
        $sql_condition = ' WHERE '.implode(' AND ', $conditions);
    } else {
        $sql_condition = '';
    }

    $sql = "SELECT dgc.id, dgc.idgroup, dgc.idservice, dgc.servicestate".
        " FROM dependencies_groups_content dgc".
        $sql_condition;
    $query = $DB->prepare($sql);
    $query->execute($params);

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

    if (isset($options['one_record']) === true) {
        $records = $query->fetchAll();
        if (isset($records[0]) === true) {
            return $records[0];
        } else {
            return false;
        }
    }

    return $query->fetchAll();
}

// TODO: split this function
function get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($idservice) {
    global $DB;

    $groups = array();

    $sql = "SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message".
            " FROM dependencies_groups dg, dependencies_messages dm".
            " WHERE dm.id=dg.idmessage".
            " AND dg.idservice=?".
            " ORDER BY dg.groupstate, dg.redundant DESC, dg.name";
    $query = $DB->prepare($sql);
    $query->execute(array($idservice));
    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');

    while ($group = $query->fetch()) {
        if (!isset($groups[$group->groupstate])) {
            $groups[$group->groupstate] = array();
        }

        // load content
        $sql = "SELECT dgc.id, dgc.idgroup, dgc.idservice, s.name, dgc.servicestate".
            " FROM dependencies_groups_content dgc, services s".
            " WHERE s.id=dgc.idservice".
            " AND dgc.idgroup=?".
            " ORDER BY dgc.servicestate DESC, s.name";
        $contents = $DB->prepare($sql);
        $contents->execute(array($group->id));
        $group->contents = $contents->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

        $groups[$group->groupstate][$group->id] = $group;
    }

    return $groups;
}

function get_dependency_message($id) {
    global $DB;

    $sql = "SELECT id, message".
            " FROM dependencies_messages".
            " WHERE id=?";
    $query = $DB->prepare($sql);
    $query->execute(array($id));

    return $query->fetch(PDO::FETCH_OBJ);
}
