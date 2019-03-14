<?php

namespace UniversiteRennes2\Isou;

class Service {
    public $id;
    public $name;
    public $url;
    public $state;
    public $comment;
    public $enable;
    public $visible;
    public $locked;
    public $rsskey;
    public $timemodified;
    public $idplugin;
    public $idcategory;
    public $category;

    public function __construct() {
        if (isset($this->id) === false) {
            // Instance manuelle.
            $this->id = 0;
            $this->name = '';
            $this->url = null;
            $this->state = State::OK;
            $this->comment = null;
            $this->enable = '1';
            $this->visible = '1';
            $this->locked = '0';
            $this->rsskey = null;
            $this->timemodified = strftime('%FT%T');
            $this->idplugin = PLUGIN_ISOU;
            $this->idcategory = null;
            $this->category = '';
        }

        $this->is_closed = ($this->state === State::CLOSED);
        $this->is_forced = ($this->locked === '1');

        $this->dependencies = null;
        $this->reverse_dependencies = null;
    }

    public function __tostring() {
        return $this->name.' (id: '.$this->id.')';
    }

    public function check_data() {
        $errors = array();

        $this->name = htmlentities($this->name, ENT_NOQUOTES, 'UTF-8');
        if ($this->name === '') {
            $errors[] = 'Le nom du service ne peut pas être vide.';
        }

        $plugin = Plugin::get_record(array('id' => $this->idplugin, 'active' => true));
        if ($plugin === false) {
            $errors[] = 'Le type de service choisi est invalide.';
        }

        if (in_array($this->visible, array('0', '1'), $strict = true) === false) {
            $errors[] = 'La valeur choisie pour la visibilité n\'est pas valide.';
        }

        if (in_array($this->locked, array('0', '1'), $strict = true) === false) {
            $errors[] = 'La valeur choisie pour le verrouillage n\'est pas valide.';
        }

        if ($this->idplugin === PLUGIN_ISOU) {
            if ($this->url === '') {
                $this->url = null;
            }

            // TODO: créer une fonction set_rsskey().
            if ($this->rsskey === null) {
                global $DB;

                $sql = 'SELECT rsskey FROM services WHERE rsskey IS NOT NULL ORDER BY rsskey DESC';
                $query = $DB->query($sql);

                $key = $query->fetch(\PDO::FETCH_OBJ);
                if (isset($key->rsskey) === true) {
                    $this->rsskey = ++$key->rsskey;
                } else {
                    $this->rsskey = 1;
                    // $errors[] = 'La clé rss n\'a pu être générée.';
                }
            }

            $category = Category::get_record(array('id' => $this->idcategory));
            if ($category === false) {
                $errors[] = 'La catégorie choisie est invalide.';
            }
        } else {
            $this->url = null;
            $this->enable = 0;
            $this->visible = 0;
            $this->rsskey = null;
            $this->idcategory = null;
        }

        return $errors;
    }

    /**
     * @param array $options Array in format:
     *
     * @see function get_records()
     * Note : fetch_one param is always set at true
     *
     * @return UniversiteRennes2\Isou\Service|false
     */
    public static function get_record($options = array()) {
        if (isset($options['id']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] est requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
     * @param array $options Array in format:
     *   category => int : category id
     *   enable => bool
     *   id => int : service id
     *   locked => bool
     *   fetch_one => bool
     *   visible => bool
     *   type => int : index key from UniversiteRennes2\Isou\Service::$TYPES
     *
     * @return UniversiteRennes2\Isou\Service[]|UniversiteRennes2\Isou\Service|false
     */
    public static function get_records($options = array()) {
        global $DB;

        $joins = array();
        $conditions = array();
        $parameters = array();

        // Parcours les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
                $conditions[] = 's.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['enable']) === true) {
            if (is_bool($options['enable']) === true) {
                $conditions[] = 's.enable = :enable';
                $parameters[':enable'] = intval($options['enable']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'enable\' doit être un booléan. Valeur donnée : '.var_export($options['enable'], $return = true));
            }

            unset($options['enable']);
        }

        if (isset($options['locked']) === true) {
            if (is_bool($options['locked']) === true) {
                $conditions[] = 's.locked = :locked';
                $parameters[':locked'] = intval($options['locked']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'locked\' doit être un booléan. Valeur donnée : '.var_export($options['locked'], $return = true));
            }

            unset($options['locked']);
        }

        if (isset($options['state']) === true) {
            if (ctype_digit($options['state']) === true) {
                $conditions[] = 's.state = :state';
                $parameters[':state'] = $options['state'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'state\' doit être un entier. Valeur donnée : '.var_export($options['state'], $return = true));
            }

            unset($options['state']);
        }

        if (isset($options['visible']) === true) {
            if (is_bool($options['visible']) === true) {
                $conditions[] = 's.visible = :visible';
                $parameters[':visible'] = intval($options['visible']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'visible\' doit être un booléan. Valeur donnée : '.var_export($options['visible'], $return = true));
            }

            unset($options['visible']);
        }

        if (isset($options['category']) === true) {
            if (ctype_digit($options['category']) === true) {
                $conditions[] = 's.idcategory = :idcategory';
                $parameters[':idcategory'] = $options['category'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'category\' doit être un entier. Valeur donnée : '.var_export($options['category'], $return = true));
            }

            unset($options['category']);
        }

        if (isset($options['plugin']) === true) {
            if (ctype_digit($options['plugin']) === true) {
                $conditions[] = 's.idplugin = :plugin';
                $parameters[':plugin'] = $options['plugin'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'plugin\' doit être un entier. Valeur donnée : '.var_export($options['plugin'], $return = true));
            }

            unset($options['plugin']);
        }

        if (isset($options['dependencies_group']) === true) {
            if (ctype_digit($options['dependencies_group']) === true) {
                $joins[] = 'JOIN dependencies_groups_content dgc ON s.id = dgc.idservice';
                $conditions[] = 'dgc.idgroup = :dependencies_group';
                $parameters[':dependencies_group'] = $options['dependencies_group'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'dependencies_group\' doit être un entier. Valeur donnée : '.var_export($options['dependencies_group'], $return = true));
            }

            unset($options['dependencies_group']);
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
        if (isset($options['fetch_column']) === true) {
            $sql = 'SELECT s.id, s.name'.
                ' FROM services s'.
                $sql_conditions.
                ' ORDER BY UPPER(s.name)';

            $query = $DB->prepare($sql);
            $query->execute($parameters);

            return $query->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }

        $sql = 'SELECT s.id, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idplugin, s.idcategory'.
                ' FROM services s'.
                ' '.implode(' ', $joins).
                $sql_conditions.
                ' ORDER BY UPPER(s.name)';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    public function save() {
        global $DB, $LOGGER;

        $this->timemodified = strftime('%FT%T');

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        $params = array(
            ':name' => $this->name,
            ':url' => $this->url,
            ':state' => $this->state,
            ':comment' => $this->comment,
            ':enable' => $this->enable,
            ':visible' => $this->visible,
            ':locked' => $this->locked,
            ':rsskey' => $this->rsskey,
            ':timemodified' => $this->timemodified,
            ':idplugin' => $this->idplugin,
            ':idcategory' => $this->idcategory,
        );

        if ($this->id === 0) {
            $sql = 'INSERT INTO services(name, url, state, comment, enable, visible, locked, rsskey, timemodified, idplugin, idcategory)'.
                ' VALUES(:name, :url, :state, :comment, :enable, :visible, :locked, :rsskey, :timemodified, :idplugin, :idcategory)';
        } else {
            $sql = 'UPDATE services SET name=:name, url=:url, state=:state, comment=:comment, enable=:enable, visible=:visible, locked=:locked,'.
                ' rsskey=:rsskey, timemodified=:timemodified, idplugin=:idplugin, idcategory=:idcategory WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
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

    public function delete() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        $commit = 1;

        $previous_transaction = $DB->inTransaction();
        if ($previous_transaction === false) {
            $DB->beginTransaction();
        }

        // Suppression des évènements, des dépendances et du service.
        $queries = array();
        $queries[] = 'DELETE FROM events WHERE idservice = :id';
        $queries[] = 'DELETE FROM dependencies_groups_content WHERE idservice = :id';
        $queries[] = 'DELETE FROM dependencies_groups WHERE idservice = :id';
        $queries[] = 'DELETE FROM services WHERE id = :id';
        foreach ($queries as $sql) {
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':id' => $this->id));
        }

        // Suppression des groupes sans contenu.
        $sql = 'DELETE FROM dependencies_groups WHERE id NOT IN (SELECT DISTINCT idgroup FROM dependencies_groups_content)';
        $query = $DB->prepare($sql);
        $commit &= $query->execute();

        if ($commit === 1) {
            if ($previous_transaction === false) {
                $DB->commit();
            }
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            if ($previous_transaction === false) {
                $DB->rollBack();
            }
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    public function change_state($state) {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET state=:state, timemodified=:timemodified WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':state' => $state, ':timemodified' => strftime('%FT%T'), ':id' => $this->id)) === false) {
            throw new \Exception(implode(', ', $query->errorInfo()));
        }

        $this->state = $state;

        $event = $this->get_current_event();
        if ($event !== false && ($this->state === State::OK || $this->state !== $event->state)) {
            $event->close();
            $event = false;
        }

        if ($event === false && $this->state !== State::OK) {
            $event = new Event();
            $event->state = $this->state;
            $event->type = Event::TYPE_UNSCHEDULED;
            $event->idservice = $this->id;

            $event->save();
        }

        return $event;
    }

    public function enable($enable = '1') {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET state=:state, enable=:enable, timemodified=:timemodified WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':state' => State::OK, ':enable' => $enable, ':timemodified' => strftime('%FT%T'), ':id' => $this->id)) === true) {
            $this->enable = $enable;
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function disable() {
        return $this->enable('0');
    }

    public function visible($visible = '1') {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET visible=:visible WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':visible' => $visible, ':id' => $this->id)) === true) {
            $this->visible = '1';
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function hide() {
        return $this->visible('0');
    }

    public function lock($state) {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET state=:state, locked=1 WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':state' => $state, ':id' => $this->id)) === true) {
            $this->state = $state;
            $this->locked = '1';
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function unlock() {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET locked=0 WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':id' => $this->id)) === true) {
            $this->locked = '0';
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function get_dependencies() {
        if ($this->dependencies === null) {
            $this->set_dependencies();
        }

        return $this->dependencies;
    }

    public function set_dependencies() {
        $this->dependencies = Dependency_Group::get_records(array('service' => $this->id));
    }

    public function get_reverse_dependencies($state = null) {
        if ($this->reverse_dependencies === null) {
            $this->reverse_dependencies = Dependency_Group::get_service_reverse_dependency_groups($this->id, $state);
        }

        return $this->reverse_dependencies;
    }

    public function set_reverse_dependencies($state = null) {
        $this->reverse_dependencies = $this->get_reverse_dependencies($state);
    }

    public function get_all_events($options = array()) {
        $options['idservice'] = $this->id;

        return Event::get_records($options);
    }

    public function get_current_event($options = array()) {
        $options['idservice'] = $this->id;
        $options['finished'] = false;
        $options['fetch_one'] = true;

        return Event::get_records($options);
    }

    public function get_closed_event($options = array()) {
        $options['idservice'] = $this->id;
        $options['fetch_one'] = true;

        return Event::get_records($options);
    }

    public function get_regular_events($options = array()) {
        $options['idservice'] = $this->id;
        $options['regular'] = true;

        return Event::get_records($options);
    }
}
