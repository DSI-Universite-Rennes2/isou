<?php

namespace UniversiteRennes2\Isou;

class Dependency_Group_Content {
    public $id;
    public $idgroup;
    public $idservice;
    public $servicestate;

    public function __construct() {
        if (isset($this->idgroup) === false) {
            // Instance manuelle.
            $this->id = 0;
            $this->idgroup = 0;
            $this->idservice = 0;
            $this->servicestate = 1;
        }
    }

    public function check_data($groups, $services, $states) {
        $errors = array();

        if (isset($groups[$this->idgroup]) === false) {
            $errors[] = 'Le groupe choisi est invalide.';
        }

        $found = 0;
        foreach ($services as $subservices) {
            $found |= isset($subservices[$this->idservice]);
        }

        if ($found === 0) {
            $errors[] = 'Le service choisi est invalide.';
        }

        if (!isset($states[$this->servicestate])) {
            $errors[] = 'L\'état choisi est invalide.';
        }

        return $errors;
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

        $parameters = array();
        $conditions = array();

        // Parcours les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
                $conditions[] = 'dgc.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['group']) === true) {
            if (ctype_digit($options['group']) === true) {
                $conditions[] = 'dgc.idgroup = :idgroup';
                $parameters[':idgroup'] = $options['group'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'group\' doit être un entier. Valeur donnée : '.var_export($options['group'], $return = true));
            }

            unset($options['group']);
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
        $sql = 'SELECT dgc.id, dgc.idgroup, dgc.idservice, dgc.servicestate'.
            ' FROM dependencies_groups_content dgc'.
            $sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    public function save() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
            );

        $params = array(
            ':idgroup' => $this->idgroup,
            ':idservice' => $this->idservice,
            ':state' => $this->servicestate,
            );

        if ($this->id === 0) {
            $sql = 'INSERT INTO dependencies_groups_content(idgroup, idservice, servicestate) VALUES(:idgroup, :idservice, :state)';
        } else {
            $sql = 'UPDATE dependencies_groups_content SET idgroup = :idgroup, idservice = :idservice, servicestate = :state WHERE id = :id';
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

    public function change_state($state) {
        global $DB;

        $results = array(
            'successes' => array(),
            'errors' => array(),
            );

        $params = array(
            ':state' => $state,
            ':idgroup' => $this->idgroup,
            ':idservice' => $this->idservice,
            );

        $sql = 'UPDATE dependencies_groups_content SET servicestate=:state WHERE idgroup = :idgroup AND idservice = :idservice';
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
        }

        return $results;
    }

    public function delete() {
        global $DB;

        $results = array(
            'successes' => array(),
            'errors' => array(),
            );

        $sql = 'DELETE FROM dependencies_groups_content WHERE idgroup = :idgroup AND idservice = :idservice';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':idgroup' => $this->idgroup, ':idservice' => $this->idservice))) {
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }
}
