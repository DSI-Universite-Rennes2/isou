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
 * Classe décrivant une description d'un évènement.
 */
class Event_Description {
    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Description de l'évènement.
     *
     * @var string
     */
    public $description;

    /**
     * Témoin indiquant si la description a été générée automatiquement. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $autogen;

    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->id) === false) {
            $this->id = '0';
            $this->description = '';
            $this->autogen = 0;
        }
    }

    /**
     * Représentation textuelle de la classe.
     *
     * @return string
     */
    public function __tostring() {
        return $this->description;
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Event_Description|false
     */
    public static function get_record(array $options = array()) {
        if (isset($options['id']) === false && isset($options['description']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] ou $options[\'description\'] est requis.');
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
     * @return Event_Description[]|Event_Description|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
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
    /**
     * Enregistre l'objet en base de données.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors de l'enregistrement.
     *
     * @return void
     */
    public function save() {
        global $DB, $LOGGER;

        $params = array(
            ':description' => $this->description,
            ':autogen' => $this->autogen,
        );

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO events_descriptions(description, autogen) VALUES(:description, :autogen)';
        } else {
            $sql = 'UPDATE events_descriptions SET description=:description, autogen=:autogen WHERE id = :id';
            $params[':id'] = $this->id;
        }

        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
            if (empty($this->id) === true) {
                $this->id = $DB->lastInsertId();
            }
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de la description de l\'évènement.');
        }
    }

    /**
     * Supprime l'objet en base de données.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors de la suppression.
     *
     * @return void
     */
    public function delete() {
        global $DB, $LOGGER;

        $sql = 'DELETE FROM events_descriptions WHERE id = :id';
        $query = $DB->prepare($sql);

        if ($query->execute(array(':id' => $this->id)) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de la description d\'évènement.');
        }
    }
}
