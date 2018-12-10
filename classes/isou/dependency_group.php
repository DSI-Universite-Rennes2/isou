<?php

namespace UniversiteRennes2\Isou;

class Dependency_Group {
    public $id;
    public $name;
    public $redundant;
    public $groupstate;
    public $idservice;
    public $service;
    public $idmessage;
    public $message;

    public function __construct() {
        if (isset($this->id) === false) {
            // Instance manuelle.
            $this->id = 0;
            $this->name = 'Groupe de dépendances';
            $this->redundant = 0;
            $this->groupstate = State::WARNING;
            $this->idservice = 1;
            $this->service = '';
            $this->idmessage = 1;
            $this->message = null;
        }
    }

    public function check_data($redundants, $states, $services) {
        $errors = array();

        $this->name = htmlentities($this->name, ENT_NOQUOTES, 'UTF-8');
        if ($this->name === '') {
            $errors[] = 'Le nom du groupe ne peut pas être vide.';
        }

        if (!isset($redundants[$this->redundant])) {
            $errors[] = 'La valeur "redondée" choisie est invalide.';
        }

        if (!isset($states[$this->groupstate])) {
            $errors[] = 'L\'état choisi est invalide.';
        }

        if (!isset($services[$this->idservice])) {
            $errors[] = 'Le service choisi est invalide.';
        }

        $this->message = trim($this->message);
        $this->idmessage = $this->get_message();
        if ($this->idmessage === false) {
            $this->idmessage = $this->set_message();
            if ($this->idmessage === false) {
                $errors[] = 'Le message n\'a pas pu être inséré en base de données.';
            }
        }

        return $errors;
    }

    // TODO: split this function
    public static function get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($idservice) {
        global $DB;

        $groups = array();

        $sql = 'SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message'.
            ' FROM dependencies_groups dg'.
            ' JOIN dependencies_messages dm ON dm.id = dg.idmessage'.
            ' WHERE dg.idservice = :idservice'.
            ' ORDER BY dg.groupstate, dg.redundant DESC, dg.name';
        $query = $DB->prepare($sql);
        $query->execute(array(':idservice' => $idservice));
        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');

        while ($group = $query->fetch()) {
            if (isset($groups[$group->groupstate]) === false) {
                $groups[$group->groupstate] = array();
            }

            // Load content.
            $sql = 'SELECT dgc.id, dgc.idgroup, dgc.idservice, s.name, dgc.servicestate'.
                ' FROM dependencies_groups_content dgc'.
                ' JOIN services s ON s.id = dgc.idservice'.
                ' WHERE dgc.idgroup = :idgroup'.
                ' ORDER BY dgc.servicestate DESC, s.name';
            $contents = $DB->prepare($sql);
            $contents->execute(array(':idgroup' => $group->id));
            $group->contents = $contents->fetchAll(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

            $groups[$group->groupstate][$group->id] = $group;
        }

        return $groups;
    }

    // TODO: remplacer par Dependency_Message::get_record
    public function get_message() {
        global $DB;

        $sql = 'SELECT id FROM dependencies_messages WHERE message = :message';
        $query = $DB->prepare($sql);
        $query->execute(array(':message' => $this->message));
        if ($message = $query->fetch(\PDO::FETCH_OBJ)) {
            return $message->id;
        } else {
            return false;
        }
    }

    public static function get_record($options = array()) {
        if (isset($options['id']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] est requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    public static function get_records($options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcours les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
                $conditions[] = 'dg.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['service']) === true) {
            if (ctype_digit($options['service']) === true) {
                $conditions[] = 'dg.idservice = :service';
                $parameters[':service'] = $options['service'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'service\' doit être un entier. Valeur donnée : '.var_export($options['service'], $return = true));
            }

            unset($options['service']);
        }

        // Construis le WHERE.
        if (isset($conditions[0]) === true) {
            $sql_conditions = ' WHERE '.implode(' AND ', $conditions);
        } else {
            $sql_conditions = '';
        }

        // Vérifie si toutes les options ont été utilisées.
        foreach ($options as $key => $option) {
            if (in_array($key, array('fetch_column', 'fetch_one'), $strict = true) === true) {
                continue;
            }

            throw new \Exception(__METHOD__.': l\'option \''.$key.'\' n\'a pas été utilisée. Valeur donnée : '.var_export($option, $return = true));
        }

        // Construis la requête.
        $sql = 'SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage, dm.message'.
            ' FROM dependencies_groups dg'.
            ' JOIN dependencies_messages dm ON dm.id = dg.idmessage'.
            $sql_conditions.
            ' ORDER BY dg.groupstate, dg.redundant DESC, dg.name';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    public static function get_service_reverse_dependency_groups($idservice, $state = null) {
        global $DB;

        $conditions = array();
        $parameters = array();

        $conditions[] = 'dgc.idservice = :idservice';
        $parameters[':idservice'] = $idservice;

        if ($state !== null) {
            $parameters[':state'] = $state;
            $conditions[] = 'dgc.servicestate = :state';
        }

        $sql = 'SELECT dg.id, dg.name, dg.redundant, dg.groupstate, dg.idservice, dg.idmessage'.
            ' FROM dependencies_groups dg'.
            ' JOIN dependencies_groups_content dgc ON dg.id = dgc.idgroup'.
            ' WHERE '.implode(' AND ', $conditions).
            ' ORDER BY dg.groupstate, dg.redundant DESC, dg.name';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group');
    }

    public function set_message() {
        global $DB, $LOGGER;

        $sql = 'INSERT INTO dependencies_messages(message) VALUES(:message)';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':message' => $this->message))) {
            return $DB->lastInsertId();
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            return false;
        }
    }

    public function save() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
            );

        $params = array(
            ':name' => $this->name,
            ':redundant' => $this->redundant,
            ':groupstate' => $this->groupstate,
            ':idservice' => $this->idservice,
            ':idmessage' => $this->idmessage,
            );

        if ($this->id === 0) {
            $sql = 'INSERT INTO dependencies_groups(name, redundant, groupstate, idservice, idmessage) VALUES(:name, :redundant, :groupstate, :idservice, :idmessage)';
        } else {
            $sql = 'UPDATE dependencies_groups SET name=:name, redundant=:redundant, groupstate=:groupstate, idservice=:idservice, idmessage=:idmessage WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            if ($this->id === 0) {
                $this->id = $DB->lastInsertId();
            }
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
        }

        return $results;
    }

    public function duplicate() {
        global $DB;

        $contents = Dependency_Group_Content::get_records(array('group' => $this->id));

        // Crée un nouveau groupe.
        $group = clone $this;
        $group->id = 0;

        if ($this->groupstate === State::WARNING) {
            $group->groupstate = State::CRITICAL;
        } else {
            $group->groupstate = State::WARNING;
        }

        $results = $group->save();

        // Inverse les contenus du groupe.
        foreach ($contents as $content) {
            $content->id = 0;
            $content->idgroup = $group->id;
            if ($content->servicestate === State::WARNING) {
                $content->servicestate = State::CRITICAL;
            } else {
                $content->servicestate = State::WARNING;
            }

            $content->save();
        }

        return $results;
    }

    public function delete() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
            );

        $commit = 1;

        $DB->beginTransaction();

        $queries = array();
        $queries[] = 'DELETE FROM dependencies_groups WHERE id = :id';
        $queries[] = 'DELETE FROM dependencies_groups_content WHERE idgroup = :id';

        foreach ($queries as $sql) {
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':id' => $this->id));
        }

        if ($commit === 1) {
            $DB->commit();
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $DB->rollBack();
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    public function is_up() {
        global $DB;

        $services = Service::get_records(array('dependencies_group' => $this->id));

        foreach ($services as $service) {
            $sql = 'SELECT idgroup, idservice, servicestate'.
                ' FROM dependencies_groups_content dgc'.
                ' WHERE idgroup = :idgroup'.
                ' AND idservice = :idservice'.
                ' AND servicestate <= :servicestate';

            $query = $DB->prepare($sql);
            $query->execute(array(':idgroup' => $this->id, ':idservice' => $service->id, ':servicestate' => $service->state));
            $status = $query->fetch(\PDO::FETCH_OBJ);

            if ($status !== false && $this->redundant === '0') {
                // Le groupe n'est pas redondé et au moins un service ne fonctionne pas... Le groupe ne fonctionne pas.
                return false;
            } elseif ($status === false && $this->redundant === '1') {
                // Le groupe est redondé et au moins un service fonctionne... Le groupe fonctionne.
                return true;
            }
        }

        if ($this->redundant === '0') {
            // Le groupe n'est pas redondé et tous les services fonctionne. Le groupe fonctionne.
            return true;
        } else {
            // Le groupe est redondé et aucun service ne fonctionne. Le groupe ne fonctionne pas.
            return false;
        }
    }
}
