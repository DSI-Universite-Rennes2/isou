<?php

namespace UniversiteRennes2\Isou;

class Announcement {
    public $message;
    public $visible;
    public $author;
    public $last_modification;

    public function __construct() {
        try {
            $this->last_modification = new \DateTime($this->last_modification);
        } catch (Exception $exception) {
            $this->last_modification = new \DateTime();
        }
    }

    public function check_data($options_visible) {
        $errors = array();

        $HTMLPurifier = new \HTMLPurifier();
        $this->message = $HTMLPurifier->purify($this->message);

        if (isset($options_visible[$this->visible]) === false) {
            $errors[] = 'La valeur du champ "afficher l\'annonce" n\'est pas valide.';
        } elseif (empty($this->message) === true) {
            $this->visible = '0';
        }

        return $errors;
    }

    public static function get_record($options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcours les options.
        if (isset($options['empty']) === true) {
            if (is_bool($options['empty']) === true) {
                if ($options['empty'] === true) {
                    $conditions[] = 'a.message = \'\'';
                } else {
                    $conditions[] = 'a.message != \'\'';
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'empty\' doit être un booléan. Valeur donnée : '.var_export($options['empty'], $return = true));
            }

            unset($options['empty']);
        }

        if (isset($options['visible']) === true) {
            if (is_bool($options['visible']) === true) {
                $conditions[] = 'a.visible = :visible';
                $parameters[':visible'] = intval($options['visible']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'visible\' doit être un booléan. Valeur donnée : '.var_export($options['visible'], $return = true));
            }

            unset($options['visible']);
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
        $sql = 'SELECT a.id, a.message, a.visible, a.author, a.last_modification'.
            ' FROM announcement a'.
            $sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Announcement');

        return $query->fetch();
    }

    public function save() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        $parameters = array(
            ':message' => $this->message,
            ':visible' => $this->visible,
            ':author' => $this->author,
            ':last_modification' => $this->last_modification->format(\DateTime::ATOM),
        );

        $sql = 'UPDATE announcement SET message=:message, visible=:visible, author=:author, last_modification=:last_modification';
        $query = $DB->prepare($sql);
        if ($query->execute($parameters) === true) {
            if ($this->visible === '1') {
                $results['successes'][] = 'L\'annonce a bien été enregistrée.';
            } else {
                $results['successes'][] = 'L\'annonce a bien été retirée.';
            }

            $LOGGER->addInfo('Modification de l\'annonce', array('author' => $_SESSION['phpCAS']['user']));
        } else {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'][] = 'La modification n\'a pas été enregistrée !';
        }

        return $results;
    }
}
