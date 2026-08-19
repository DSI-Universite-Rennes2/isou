<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

use DateTime;
use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Classe décrivant une annonce.
 */
class Announcement {
    /**
     * Identifiant de l'annonce.
     *
     * @var integer
     */
    public $id;

    /**
     * Titre facultatif de l'annonce.
     *
     * @var string
     */
    public $title = '';

    /**
     * Message de l'annonce.
     *
     * @var string
     */
    public $message;

    /**
     * Date de début.
     *
     * @var DateTime|null|false
     */
    public $startdate;

    /**
     * Date de fin.
     *
     * @var DateTime|null|false
     */
    public $enddate;

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
     * @return string[] Retourne un tableau d'erreurs.
     */
    public function check_data() {
        $errors = array();

        $this->title = htmlentities(trim($this->title), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');

        $config = HTMLPurifier_Config::createDefault();
        // Interdit la balise <pre>.
        $config->set('HTML.ForbiddenElements', 'pre');
        // Autorise uniquement les attributs class, lang et src.
        $config->set('HTML.AllowedAttributes', '*.class,*.lang,*.src');
        // Autorise uniquement quelques classes bootstrap.
        $config->set('Attr.AllowedClasses', 'text-primary,text-secondary,text-success,text-danger,text-warning,text-info');

        $HTMLPurifier = new HTMLPurifier($config);
        $this->message = $HTMLPurifier->purify($this->message);

        if ($this->startdate === false || $this->enddate === false) {
            if ($this->startdate === false) {
                $errors[] = 'La date de début de l\'annonce doit être au format AAAA-MM-JJ HH:MM.';
            }

            if ($this->enddate === false) {
                $errors[] = 'La date de fin de l\'annonce doit être au format AAAA-MM-JJ HH:MM.';
            }
        } elseif (empty($this->message) === true) {
            $this->startdate = null;
            $this->enddate = null;
        } elseif ($this->startdate === null && $this->enddate !== null) {
            $errors[] = 'La date de début de l\'annonce doit être définie.';
        } elseif ($this->startdate !== null && $this->enddate !== null) {
            if ($this->startdate >= $this->enddate) {
                $errors[] = 'La date de début de l\'annonce doit être antérieure à la date de fin de l\'annonce.';
            }
        }

        return $errors;
    }

    /**
     * Calcule un objet datetime à partir des paramètres donnés.
     *
     * @param string $date Une chaîne au format YYYY-MM-DD.
     * @param string $time Une chaîne au format hh:mm.
     *
     * @return DateTime|null|false Retourne false en cas d'erreur, null quand aucune info n'a été donnée et datetime quand c'est bon.
     *
     * phpcs:ignore Squiz.Commenting.FunctionCommentThrowTag.Missing
     */
    public function get_datetime(string $date, string $time): Datetime|null|false {
        if (empty($date) === true || empty($time) === true) {
            return null;
        }

        try {
            $preg_match_date = preg_match('#^(?P<year>\d{4}).(?P<month>\d{2}).(?P<day>\d{2})$#', $date);
            $preg_match_time = preg_match('#^(?P<hour>\d{2}).(?P<minute>\d{2})$#', $time);

            if ($preg_match_date === 1 && $preg_match_time === 1) {
                $datetime = sprintf('%sT%s:00', $date, $time);
            } else {
                throw new Exception();
            }

            return new DateTime($datetime);
        } catch (\Exception $exception) {
            // La date de fin d\'interruption doit être au format AAAA-MM-JJ HH:MM.
            return false;
        }
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

        if (isset($options['now']) === true) {
            if (is_bool($options['now']) === true) {
                if ($options['now'] === true) {
                    $conditions[] = 'a.startdate IS NOT NULL AND a.startdate <= :now AND (a.enddate IS NULL OR a.enddate >= :now)';
                    $parameters['now'] = date('Y-m-d\TH:i:s');
                } else {
                    throw new \Exception(__METHOD__.': l\'option \'now\' ne peut pas être utilisée avec la valeur False.');
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'now\' doit être un booléan. Valeur donnée : '.var_export($options['now'], $return = true));
            }

            unset($options['now']);
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
        $sql = 'SELECT a.id, a.title, a.message, a.startdate, a.enddate, a.author, a.last_modification'.
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
        global $DB, $LOGGER, $USER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        if ($this->startdate === null) {
            $startdate = null;
        } else {
            $startdate = $this->startdate->format('Y-m-d\TH:i:s');
        }

        if ($this->enddate === null) {
            $enddate = null;
        } else {
            $enddate = $this->enddate->format('Y-m-d\TH:i:s');
        }

        $parameters = array(
            ':title' => $this->title,
            ':message' => $this->message,
            ':startdate' => $startdate,
            ':enddate' => $enddate,
            ':author' => $this->author,
            ':last_modification' => $this->last_modification->format(\DateTime::ATOM),
        );

        $sql = 'UPDATE announcement SET title=:title, message=:message, startdate=:startdate, enddate=:enddate, author=:author, last_modification=:last_modification';
        $query = $DB->prepare($sql);
        if ($query->execute($parameters) === true) {
            $results['successes'][] = 'Les modifications de l\'annonce ont bien été enregistrées.';
            $visible = self::get_record(array('empty' => false, 'now' => true));
            if (empty($visible) === false) {
                $results['successes'][] = 'Le message est visible sur les pages publiques.';
            } else {
                $results['successes'][] = 'Le message n\'est pas affichée sur les pages publiques.';
            }

            $LOGGER->info('Modification de l\'annonce', array('userid' => $USER->id, 'username' => $USER->username));
        } else {
            // Enregistre le message d'erreur dans les logs.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            $results['errors'][] = 'La modification n\'a pas été enregistrée !';
        }

        return $results;
    }
}
