<?php

namespace UniversiteRennes2\Isou;

class Dependency_Group_Content{
    public $id;
    public $idgroup;
    public $idservice;
    public $servicestate;

    public function __construct() {
        if (!isset($this->idgroup)) {
            // instance manuelle
            $this->id = 0;
            $this->idgroup = 0;
            $this->idservice = 0;
            $this->servicestate = 1;
        }
    }

    public function check_data($groups, $services, $states) {
        $errors = array();

        if (!isset($groups[$this->idgroup])) {
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
            $sql = "INSERT INTO dependencies_groups_content(idgroup, idservice, servicestate) VALUES(:idgroup, :idservice, :state)";
        } else {
            $sql = "UPDATE dependencies_groups_content SET idgroup = :idgroup, idservice = :idservice, servicestate = :state WHERE id = :id";
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // log db errors
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
        $state,
        $this->idgroup,
        $this->idservice,
        );

        $sql = "UPDATE dependencies_groups_content SET servicestate=? WHERE idgroup=? AND idservice=?";
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // log db errors
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

        $sql = "DELETE FROM dependencies_groups_content WHERE idgroup=? AND idservice=?";
        $query = $DB->prepare($sql);
        if ($query->execute(array($this->idgroup, $this->idservice))) {
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }
}
