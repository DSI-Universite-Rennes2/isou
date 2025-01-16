<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

/**
 * Classe décrivant le contenu d'un groupe de dépendances.
 */
#[\AllowDynamicProperties]
class Dependency_Group_Content {
    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Identifiant du groupe.
     *
     * @var integer
     */
    public $idgroup;

    /**
     * Identifiant du service.
     *
     * @var integer
     */
    public $idservice;

    /**
     * Identifiant de l'état du service.
     *
     * @var integer
     */
    public $servicestate;

    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->idgroup) === false) {
            // Instance manuelle.
            $this->id = '0';
            $this->idgroup = '0';
            $this->idservice = '0';
            $this->servicestate = State::WARNING;
        }
    }

    /**
     * Contrôle les données avant de les enregistrer en base de données.
     *
     * @param array $groups Tableau associatif contenant les valeurs autorisés pour la propriété "idgroup".
     * @param array $services Tableau associatif contenant les valeurs autorisés pour la propriété "idservice".
     * @param array $states Tableau associatif contenant les valeurs autorisés pour la propriété "servicestate".
     *
     * @return string[] Retourne un tableau d'erreurs.
     */
    public function check_data(array $groups, array $services, array $states) {
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

        if (isset($states[$this->servicestate]) === false) {
            $errors[] = 'L\'état choisi est invalide.';
        }

        return $errors;
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Dependency_Group_Content|false
     */
    public static function get_record(array $options = array()) {
        if (isset($options['id']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] est requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
     * Récupère un tableau d'objets en base de données en fonction des options passées en paramètre.
     *
     * Liste des options disponibles : TODO.
     *
     * @param array $options Tableau d'options.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Dependency_Group_Content[]|Dependency_Group_Content|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $parameters = array();
        $conditions = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
                $conditions[] = 'dgc.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['group']) === true) {
            if (is_string($options['group']) === true && ctype_digit($options['group']) === true) {
                $conditions[] = 'dgc.idgroup = :idgroup';
                $parameters[':idgroup'] = $options['group'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'group\' doit être un entier. Valeur donnée : '.var_export($options['group'], $return = true));
            }

            unset($options['group']);
        }

        // Construit le WHERE.
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

        // Construit la requête.
        $sql = 'SELECT dgc.id, dgc.idgroup, dgc.idservice, dgc.servicestate, s.name, s.idplugin'.
            ' FROM dependencies_groups_content dgc'.
            ' JOIN services s ON s.id = dgc.idservice'.
            ' JOIN plugins p ON p.id = s.idplugin AND p.active = 1'.
            $sql_conditions.
            ' ORDER BY s.name';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Enregistre l'objet en base de données.
     *
     * @return array
     */
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

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO dependencies_groups_content(idgroup, idservice, servicestate) VALUES(:idgroup, :idservice, :state)';
        } else {
            $sql = 'UPDATE dependencies_groups_content SET idgroup = :idgroup, idservice = :idservice, servicestate = :state WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
            if (empty($this->id) === true) {
                $this->id = $DB->lastInsertId();
            }

            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
        }

        return $results;
    }

    /**
     * Change l'état du service.
     *
     * @param string $state Identifiant de l'état à attribuer.
     *
     * @return array
     */
    public function change_state(string $state) {
        global $DB, $LOGGER;

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

        if ($query->execute($params) === true) {
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
        }

        return $results;
    }

    /**
     * Supprime l'objet en base de données.
     *
     * @return array
     */
    public function delete() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        $sql = 'DELETE FROM dependencies_groups_content WHERE idgroup = :idgroup AND idservice = :idservice';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':idgroup' => $this->idgroup, ':idservice' => $this->idservice)) === true) {
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }
}
