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
 * Classe décrivant un groupe de dépendances.
 */
#[\AllowDynamicProperties]
class Dependency_Group {
    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Nom du groupe.
     *
     * @var string
     */
    public $name;

    /**
     * Témoin indiquant si le groupe est redondé. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $redundant;

    /**
     * État du groupe (voir les constantes de la classe State pour avoir les valeurs possibles).
     *
     * @var integer
     */
    public $groupstate;

    /**
     * Identifiant du service.
     *
     * @var integer
     */
    public $idservice;

    /**
     * Nom du service.
     *
     * @var string
     */
    public $service;

    /**
     * Identifiant du message.
     *
     * @var integer
     */
    public $idmessage;

    /**
     * Contenu du message.
     *
     * @var string
     */
    public $message;

    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->id) === false) {
            // Instance manuelle.
            $this->id = '0';
            $this->name = 'Groupe de dépendances';
            $this->redundant = '0';
            $this->groupstate = State::WARNING;
            $this->idservice = '1';
            $this->service = '';
            $this->idmessage = '1';
            $this->message = '';
        }

        $this->contents = null;
    }

    /**
     * Contrôle les données avant de les enregistrer en base de données.
     *
     * @param array $redundants Tableau associatif contenant les valeurs autorisés pour la propriété "redundant".
     * @param array $states Tableau associatif contenant les valeurs autorisés pour la propriété "groupstate".
     * @param array $services Tableau associatif contenant les valeurs autorisés pour la propriété "idservice".
     *
     * @return string[] Retourne un tableau d'erreurs.
     */
    public function check_data(array $redundants, array $states, array $services) {
        $errors = array();

        $this->name = htmlentities(trim($this->name), ENT_NOQUOTES, 'UTF-8');
        if ($this->name === '') {
            $errors[] = 'Le nom du groupe ne peut pas être vide.';
        }

        if (isset($redundants[$this->redundant]) === false) {
            $errors[] = 'La valeur "redondée" choisie est invalide.';
        }

        if (isset($states[$this->groupstate]) === false) {
            $errors[] = 'L\'état choisi est invalide.';
        }

        if (isset($services[$this->idservice]) === false) {
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

    /**
     * Retourne le contenu de groupe.
     *
     * @return Dependency_Group_Content[]
     */
    public function get_contents() {
        if ($this->contents === null) {
            $this->set_contents();
        }

        return $this->contents;
    }

    /**
     * Définit le contenu de ce groupe.
     *
     * @return void
     */
    public function set_contents() {
        $this->contents = Dependency_Group_Content::get_records(array('group' => $this->id));
    }

    /**
     * Retourne les groupes de dépendances et leur contenu triés par état.
     *
     * TODO: scinder cette fonction.
     *
     * @param string $idservice Identifiant du service.
     *
     * @return integer|false
     */
    public static function get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags(string $idservice) {
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

        foreach ($query->fetchAll() as $group) {
            if (isset($groups[$group->groupstate]) === false) {
                $groups[$group->groupstate] = array();
            }

            // Load content.
            $sql = 'SELECT dgc.id, dgc.idgroup, dgc.idservice, s.name, p.name AS pluginname, dgc.servicestate'.
                ' FROM dependencies_groups_content dgc'.
                ' JOIN services s ON s.id = dgc.idservice'.
                ' JOIN plugins p ON p.id = s.idplugin AND p.active = 1'.
                ' WHERE dgc.idgroup = :idgroup'.
                ' ORDER BY dgc.servicestate DESC, s.name';
            $contents = $DB->prepare($sql);
            $contents->execute(array(':idgroup' => $group->id));
            $group->contents = $contents->fetchAll(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Dependency_Group_Content');

            $groups[$group->groupstate][$group->id] = $group;
        }

        return $groups;
    }

    /**
     * Retourne l'identifiant d'un message.
     *
     * TODO: remplacer par Dependency_Message::get_record.
     *
     * @return integer|false
     */
    public function get_message() {
        global $DB;

        $sql = 'SELECT id FROM dependencies_messages WHERE message = :message';
        $query = $DB->prepare($sql);
        $query->execute(array(':message' => $this->message));

        $message = $query->fetch(\PDO::FETCH_OBJ);
        if (isset($message->id) === true) {
            return $message->id;
        } else {
            return false;
        }
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Dependency_Group|false
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
     * @return Dependency_Group[]Dependency_Group|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
                $conditions[] = 'dg.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['service']) === true) {
            if (is_string($options['service']) === true && ctype_digit($options['service']) === true) {
                $conditions[] = 'dg.idservice = :service';
                $parameters[':service'] = $options['service'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'service\' doit être un entier. Valeur donnée : '.var_export($options['service'], $return = true));
            }

            unset($options['service']);
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

    /**
     * Retourne les groupes de dépendances inversés.
     *
     * @param string $idservice Identifiant du service.
     * @param string $state Identifiant de l'état.
     *
     * @return array
     */
    public static function get_service_reverse_dependency_groups(string $idservice, string $state = null) {
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

    /**
     * Enregistre un message en base de données.
     *
     * @return string|false
     */
    public function set_message() {
        global $DB, $LOGGER;

        $sql = 'INSERT INTO dependencies_messages(message) VALUES(:message)';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':message' => $this->message)) === true) {
            return $DB->lastInsertId();
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            return false;
        }
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
            ':name' => $this->name,
            ':redundant' => $this->redundant,
            ':groupstate' => $this->groupstate,
            ':idservice' => $this->idservice,
            ':idmessage' => $this->idmessage,
        );

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO dependencies_groups(name, redundant, groupstate, idservice, idmessage) VALUES(:name, :redundant, :groupstate, :idservice, :idmessage)';
        } else {
            $sql = 'UPDATE dependencies_groups SET name=:name, redundant=:redundant, groupstate=:groupstate, idservice=:idservice, idmessage=:idmessage WHERE id = :id';
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
     * Duplique un groupe de dépendances.
     *
     * @return array
     */
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
            $LOGGER->error(implode(', ', $query->errorInfo()));

            $DB->rollBack();
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    /**
     * Détermine si le groupe est en état de fonctionnement.
     *
     * @return boolean
     */
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
