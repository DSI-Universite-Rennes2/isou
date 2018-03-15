<?php

/**
  * @param array $options Array in format:
  * @see function get_services()
  * Note : one_record param is always set at true
  *
  * @return array of UniversiteRennes2\Isou\Events
  */
function get_service($options = array()) {
    $options['one_record'] = true;

    return get_services($options);
}

/**
  * @param array $options Array in format:
  *   category        => int : category id
  *   enable          => bool
  *   id              => int : service id
  *   locked          => bool
  *   one_record      => bool
  *   visible         => bool
  *   type            => int : index key from UniversiteRennes2\Isou\Service::$TYPES
  *
  * @return array of UniversiteRennes2\Isou\Events
  */
function get_services($options = array()) {
    global $DB, $LOGGER;

    $params = array();
    $conditions = array();

    if (isset($options['category'])) {
        if (ctype_digit($options['category'])) {
            $conditions[] = 's.idcategory = ?';
            $params[] = $options['category'];
        } else {
            $LOGGER->addInfo('L\'option \'category\' doit être un entier.', array('value', $options['category']));
        }
    }

    if (isset($options['enable'])) {
        if (is_bool($options['enable'])) {
            $conditions[] = 's.enable = ?';
            $params[] = intval($options['enable']);
        } else {
            $LOGGER->addInfo('L\'option \'enable\' doit être un booléan.', array('value', $options['enable']));
        }
    }

    if (isset($options['id'])) {
        if (ctype_digit($options['id'])) {
            $conditions[] = 's.id = ?';
            $params[] = $options['id'];
        } else {
            $LOGGER->addInfo('L\'option \'id\' doit être un entier.', array('value', $options['id']));
        }
    }

    if (isset($options['locked'])) {
        if (is_bool($options['locked'])) {
            $conditions[] = 's.locked = ?';
            $params[] = intval($options['locked']);
        } else {
            $LOGGER->addInfo('L\'option \'locked\' doit être un booléan.', array('value', $options['locked']));
        }
    }

    if (isset($options['state'])) {
        if (ctype_digit($options['state'])) {
            $conditions[] = 's.state = ?';
            $params[] = $options['state'];
        } else {
            $LOGGER->addInfo('L\'option \'state\' doit être un entier.', array('value', $options['state']));
        }
    }

    if (isset($options['visible'])) {
        if (is_bool($options['visible'])) {
            $conditions[] = 's.visible = ?';
            $params[] = intval($options['visible']);
        } else {
            $LOGGER->addInfo('L\'option \'visible\' doit être un booléan.', array('value', $options['visible']));
        }
    }

    if (isset($options['plugin']) === true) {
        if (ctype_digit($options['plugin']) === true) {
            $conditions[] = 's.idplugin = ?';
            $params[] = $options['plugin'];
        } else {
            $LOGGER->addInfo('L\'option \'plugin\' doit être un entier.', array('value', $options['plugin']));
        }
    }

    if (isset($conditions[0])) {
        $sql_condition = ' WHERE '.implode(' AND ', $conditions);
    } else {
        $sql_condition = '';
    }

    $sql = 'SELECT s.id, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idplugin, s.idcategory'.
            ' FROM services s'.
            ' '.$sql_condition.
            ' ORDER BY UPPER(s.name)';
    $query = $DB->prepare($sql);
    $query->execute($params);

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');

    if (isset($options['one_record'])) {
        $services = $query->fetchAll();
        if (isset($services[0])) {
            return $services[0];
        } else {
            return false;
        }
    }

    return $query->fetchAll();
}

function get_services_sorted_by_id($type = null) {
    global $DB;

    if ($type === null) {
        $sql = "SELECT id, name FROM services ORDER BY UPPER(name)";
        $params = array();
    } else {
        $sql = "SELECT id, name FROM services WHERE idplugin=? ORDER BY UPPER(name)";
        $params = array($type);
    }
    $query = $DB->prepare($sql);
    $query->execute($params);
    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

function get_services_sorted_by_idtype() {
    global $CFG, $DB;

    $sql = "SELECT s.id, s.name, s.idplugin".
        " FROM services s".
        " ORDER BY UPPER(s.name)";
    $query = $DB->prepare($sql);
    $query->execute();

    $plugins = Plugin::get_plugins(array('active' => true));

    $services = array();
    foreach ($plugins as $plugin) {
        $services['Services '.$plugin->name] = array();
    }

    while ($service = $query->fetch(PDO::FETCH_OBJ)) {
        if (isset($services[$service->idplugin]) === false) {
            $services[$service->idplugin] = array();
        }

        $services[$service->idplugin][] = $service->name;
    }

    return $services;
}

function get_isou_services_sorted_by_idtype() {
    global $DB;

    $sql = "SELECT s.id, s.name".
        " FROM services s".
        " WHERE s.idplugin=?".
        " ORDER BY UPPER(s.name)";
    $query = $DB->prepare($sql);
    $query->execute(array(PLUGIN_ISOU));

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

function get_services_by_dependencies_group($idgroup) {
    global $DB;

    $sql = "SELECT s.id, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idplugin, s.idcategory, dgc.servicestate".
            " FROM services s, dependencies_groups_content dgc".
            " WHERE s.id=dgc.idservice".
            " AND dgc.idgroup=?".
            " ORDER BY UPPER(s.name)";
    $query = $DB->prepare($sql);
    $query->execute(array($idgroup));

    return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');
}
