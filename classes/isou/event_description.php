<?php

namespace UniversiteRennes2\Isou;

class Event_Description {
    public $id;
    public $description;
    public $autogen;

    public function __construct() {
        if (isset($this->id) === false) {
            $this->id = 0;
            $this->description = '';
            $this->autogen = 0;
        }
    }

    public function __tostring() {
        return $this->description;
    }

    public static function get_record($options = array()) {
        if (isset($options['id']) === false && isset($options['description']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] ou $options[\'description\'] est requis.');
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
                $conditions[] = 'ed.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['description']) === true) {
            if (is_string($options['description']) === true) {
                $conditions[] = 'ed.description = :description';
                $parameters[':description'] = $options['description'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'description\' doit être un entier. Valeur donnée : '.var_export($options['description'], $return = true));
            }

            unset($options['description']);
        }

        if (isset($options['autogen']) === true) {
            if (is_bool($options['autogen']) === true) {
                $conditions[] = 'ed.autogen = :autogen';
                $parameters[':autogen'] = intval($options['autogen']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'autogen\' doit être un booléen. Valeur donnée : '.var_export($options['autogen'], $return = true));
            }

            unset($options['autogen']);
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
        $sql = 'SELECT ed.id, ed.description, ed.autogen'.
            ' FROM events_descriptions ed'.
            $sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event_Description');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    public function save() {
        global $DB, $LOGGER;

        $params = array(
            ':description' => $this->description,
            ':autogen' => $this->autogen,
        );

        if ($this->id === 0) {
            $sql = 'INSERT INTO events_descriptions(description, autogen) VALUES(:description, :autogen)';
        } else {
            $sql = 'UPDATE events_descriptions SET description=:description, autogen=:autogen WHERE id = :id';
            $params[':id'] = $this->id;
        }

        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
            if ($this->id === 0) {
                $this->id = $DB->lastInsertId();
            }
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de la description de l\'évènement.');
        }
    }

    public function delete() {
        global $DB, $LOGGER;

        $sql = 'DELETE FROM events_descriptions WHERE id = :id';
        $query = $DB->prepare($sql);

        if ($query->execute(array(':id' => $this->id)) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de la description d\'évènement.');
        }
    }
}
