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
 * Classe décrivant une annonce.
 */
class Announcement {
    /**
     * Message de l'annonce.
     *
     * @var string
     */
    public $message;

    /**
     * Témoin indiquant si l'annonce est affichée ou non. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $visible;

    /**
     * Nom utilisateur de l'auteur de l'annonce.
     *
     * @var string
     */
    public $author;

    /**
     * Date de la dernière modification de l'annonce.
     *
     * @var \DateTime
     */
    public $last_modification;

    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        try {
            $last_modification = '';
            if (isset($this->last_modification) === true) {
                $last_modification = $this->last_modification;
            }
            $this->last_modification = new \DateTime($last_modification);
        } catch (Exception $exception) {
            $this->last_modification = new \DateTime();
        }
    }

    /**
     * Contrôle les données avant de les enregistrer en base de données.
     *
     * @param string[] $options_visible Tableau associatif contenant les valeurs autorisés pour la propriété "visible".
     *
     * @return string[] Retourne un tableau d'erreurs.
     */
    public function check_data(array $options_visible) {
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

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Announcement|false
     */
    public static function get_record(array $options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcourt les options.
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
        $sql = 'SELECT a.id, a.message, a.visible, a.author, a.last_modification'.
            ' FROM announcement a'.
            $sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Announcement');

        return $query->fetch();
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

            $LOGGER->info('Modification de l\'annonce', array('author' => $_SESSION['phpCAS']['user']));
        } else {
            // Enregistre le message d'erreur dans les logs.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            $results['errors'][] = 'La modification n\'a pas été enregistrée !';
        }

        return $results;
    }
}
