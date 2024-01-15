<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

use DateInterval;
use DateTime;
use IntlDateFormatter;
use Smarty;
use stdClass;

/**
 * Classe décrivant un évènement.
 */
#[\AllowDynamicProperties]
class Event {
    const PERIOD_NONE = '0';
    const PERIOD_DAILY = '86400';
    const PERIOD_WEEKLY = '604800';

    const TYPE_UNSCHEDULED = '0';
    const TYPE_SCHEDULED = '1';
    const TYPE_REGULAR = '2';
    const TYPE_CLOSED = '3';

    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Date de début.
     *
     * @var \DateTime
     */
    public $startdate;

    /**
     * Date de fin.
     *
     * @var \DateTime|null
     */
    public $enddate;

    /**
     * Identifiant de l'état.
     *
     * @var integer
     */
    public $state;

    /**
     * Identifiant du type d'évènement.
     *
     * @var integer
     */
    public $type;

    /**
     * Identifiant de la période.
     *
     * @var integer|null
     */
    public $period;

    /**
     * Identifiant de la description.
     *
     * @var integer
     */
    public $ideventdescription;

    /**
     * Description.
     *
     * @var string
     */
    public $description;

    /**
     * Identifiant du service.
     *
     * @var integer
     */
    public $idservice;

    /**
     * Liste des types d'évènements.
     *
     * @var string[]
     */
    public static $TYPES = array(
        self::TYPE_SCHEDULED => 'Évènement prévu',
        self::TYPE_UNSCHEDULED => 'Évènement imprévu',
        self::TYPE_REGULAR => 'Évènement régulier',
        self::TYPE_CLOSED => 'Service fermé',
    );

    /**
     * Liste des périodes.
     *
     * @var string[]
     */
    public static $PERIODS = array(
        self::PERIOD_NONE => 'Aucune',
        self::PERIOD_DAILY => 'Tous les jours',
        self::PERIOD_WEEKLY => 'Toutes les semaines',
    );

    /**
     * Constructeur de la classe.
     *
     * TODO: à simplifier.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->id) === true) {
            // Instance PDO.
            try {
                $this->startdate = new \DateTime($this->startdate);
                if ($this->enddate !== null) {
                    $this->enddate = new \DateTime($this->enddate);
                }
            } catch (\Exception $exception) {
                $this->startdate = new \DateTime();
                $this->enddate = new \DateTime();
            }

            if (empty($this->period) === true) {
                $this->period = self::PERIOD_NONE;
            }
        } else {
            // Instance manuelle.
            $this->id = '0';
            $this->startdate = new \DateTime();
            $this->enddate = null;
            $this->state = State::CRITICAL;
            $this->type = self::TYPE_SCHEDULED;
            $this->period = self::PERIOD_NONE;
            $this->ideventdescription = '1';
            $this->description = null;
            $this->idservice = '0';
        }
    }

    /**
     * Représentation textuelle de la classe.
     *
     * @return string
     */
    public function __tostring() {
        $str = '';

        if ($this->state === State::CLOSED) {
            $str = 'Service fermé depuis le '.IntlDateFormatter::formatObject($this->startdate, 'eeee dd MMMM y').'.';
            if ($this->enddate !== null) {
                $str .= ' Réouverture le '.IntlDateFormatter::formatObject($this->enddate, 'eeee dd MMMM y').'.';
            }
        } elseif (empty($this->period) === false) {
            $starttime = $this->startdate->format('H\hi');
            $endtime = $this->enddate->format('H\hi');

            switch ($this->period) {
                case self::PERIOD_WEEKLY:
                    $str = 'Tous les '.IntlDateFormatter::formatObject($this->startdate, 'eeee').' de '.$starttime.' à '.$endtime.'.';
                    break;
                case self::PERIOD_DAILY:
                default:
                    $str = 'Tous les jours de '.$starttime.' à '.$endtime.'.';
            }
        } else {
            $starttime = $this->startdate->format('H\hi');
            $startday = IntlDateFormatter::formatObject($this->startdate, 'eeee dd MMMM');

            if ($this->type === self::TYPE_SCHEDULED) {
                $type = 'en maintenance';
            } else {
                $type = 'perturbé';
            }

            if ($this->startdate->getTimestamp() > TIME) {
                // Évènement futur.
                if ($this->enddate === null) {
                    $str = 'Le service sera '.$type.' le '.$startday.' à partir de '.$starttime.'.';
                } elseif ($this->startdate->format('Ymd') === $this->enddate->format('Ymd')) {
                    // Même journée.
                    $str = 'Le service sera '.$type.' le '.$startday.' de '.$this->startdate->format('G\hi').' à '.$this->enddate->format('G\hi').'.';
                } else {
                    $endtime = $this->enddate->format('H\hi');
                    $endday = IntlDateFormatter::formatObject($this->enddate, 'eeee dd MMMM');
                    $str = 'Le service sera '.$type.' du '.$startday.' '.$starttime.' au '.$endday.' '.$endtime.'.';
                }
            } elseif ($this->enddate !== null && $this->enddate->getTimestamp() < TIME) {
                $endtime = $this->enddate->format('H\hi');

                // Évènement passé.
                if ($this->startdate->format('Ymd') === $this->enddate->format('Ymd')) {
                    // Évènement qui s'est déroulé sur une journée.
                    $str = 'Le service a été '.$type.' le '.$startday.' de '.$starttime.' à '.$endtime.'.';
                } else {
                    // Évènement qui s'est déroulé sur plusieurs journées.
                    $endday = IntlDateFormatter::formatObject($this->enddate, 'eeee dd MMMM');
                    $str = 'Le service a été '.$type.' du '.$startday.' '.$starttime.' au '.$endday.' '.$endtime.'.';
                }
            } else {
                // Évènement en cours.
                if ($this->enddate === null) {
                    if ($this->startdate->format('Ymd') === date('Ymd')) {
                        $str = 'Le service est '.$type.' depuis '.$starttime.'.';
                    } else {
                        $str = 'Le service est '.$type.' depuis le '.$startday.' '.$starttime.'.';
                    }
                } else {
                    $endtime = $this->enddate->format('H\hi');
                    $endday = IntlDateFormatter::formatObject($this->enddate, 'eeee dd MMMM');
                    if (IntlDateFormatter::formatObject($this->startdate, 'eeee dd MMMM') === $endday) {
                        $str = 'Le service est '.$type.' de '.$starttime.' à '.$endtime.'.';
                    } else {
                        $str = 'Le service est '.$type.' jusqu\'au '.$endday.' '.$endtime.'.';
                    }
                }
            }
        }

        return $str;
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Event|false
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
     * Liste des options disponibles :
     *   after           => DateTime
     *   before          => DateTime
     *   idservice       => int
     *   one_record      => bool
     *   regular         => bool
     *   plugin          => int : index key from UniversiteRennes2\Isou\Service::$TYPES
     *   since           => DateTime
     *   finished        => bool
     *   state           => int : index key from UniversiteRennes2\Isou\State::$STATES
     *   tolerance       => int : seconds
     *   type            => int : index key from UniversiteRennes2\Isou\Event::$TYPES
     *   sort            => Array of strings
     *
     * @param array $options Tableau d'options.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Event[]|Event|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $joins = array();
        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
                $conditions[] = 'e.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['after']) === true) {
            if ($options['after'] instanceof \DateTime) {
                $conditions[] = 'e.startdate >= :after';
                $parameters[':after'] = $options['after']->format('Y-m-d\TH:i:s');
            } else {
                throw new \Exception(__METHOD__.': l\'option \'after\' doit être de type DateTime. Valeur donnée : '.var_export($options['after'], $return = true));
            }

            unset($options['after']);
        }

        if (isset($options['before']) === true) {
            if ($options['before'] instanceof \DateTime) {
                $conditions[] = 'e.startdate < :before';
                $parameters[':before'] = $options['before']->format('Y-m-d\TH:i:s');
            } else {
                throw new \Exception(__METHOD__.': l\'option \'before\' doit être de type DateTime. Valeur donnée : '.var_export($options['before'], $return = true));
            }

            unset($options['before']);
        }

        if (isset($options['enddate_between']) === true) {
            if (is_array($options['enddate_between']) === false) {
                throw new \Exception(__METHOD__.': l\'option \'enddate_between\' doit être de type Array. Valeur donnée : '.var_export($options['enddate_between'], $return = true));
            } elseif (isset($options['enddate_between'][1]) === false) {
                throw new \Exception(__METHOD__.': l\'option \'enddate_between\' doit contenir 2 éléments de type DateTime. Valeur donnée : '.var_export($options['enddate_between'], $return = true));
            } else {
                if (($options['enddate_between'][0] instanceof \DateTime) === false) {
                    throw new \Exception('Le premier élément de \'option \'enddate_between\' doit être de type DateTime. Valeur donnée : '.var_export($options['enddate_between'][0], $return = true));
                } elseif (($options['enddate_between'][1] instanceof \DateTime) === false) {
                    throw new \Exception('Le deuxième élément de \'option \'enddate_between\' doit être de type DateTime. Valeur donnée : '.var_export($options['enddate_between'][1], $return = true));
                } else {
                    $conditions[] = 'e.enddate BETWEEN :enddate_between0 AND :enddate_between1';
                    $parameters[':enddate_between0'] = $options['enddate_between'][0]->format('Y-m-d\TH:i:s');
                    $parameters[':enddate_between1'] = $options['enddate_between'][1]->format('Y-m-d\TH:i:s');
                }
            }

            unset($options['enddate_between']);
        }

        if (isset($options['idservice']) === true) {
            if (is_string($options['idservice']) === true && ctype_digit($options['idservice']) === true) {
                $conditions[] = 's.id = :idservice';
                $parameters[':idservice'] = $options['idservice'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'idservice\' doit être un entier. Valeur donnée : '.var_export($options['idservice'], $return = true));
            }

            unset($options['idservice']);
        }

        if (isset($options['regular']) === true) {
            if (is_bool($options['regular']) === true) {
                if ($options['regular'] === true) {
                    $conditions[] = 'e.period != :period';
                } else {
                    $conditions[] = 'e.period = :period';
                }
                $parameters[':period'] = self::PERIOD_NONE;
            } else {
                throw new \Exception(__METHOD__.': l\'option \'regular\' doit être un booléen. Valeur donnée : '.var_export($options['regular'], $return = true));
            }

            unset($options['regular']);
        }

        if (isset($options['plugin']) === true) {
            if (is_string($options['plugin']) === true && ctype_digit($options['plugin']) === true) {
                $conditions[] = 's.idplugin = :idplugin';
                $parameters[':idplugin'] = $options['plugin'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'plugin\' doit être un entier. Valeur donnée : '.var_export($options['plugin'], $return = true));
            }

            unset($options['plugin']);
        }

        if (isset($options['notplugin']) === true) {
            if (is_string($options['notplugin']) === true && ctype_digit($options['notplugin']) === true) {
                $conditions[] = 's.idplugin != :idplugin';
                $parameters[':idplugin'] = $options['notplugin'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'notplugin\' doit être un entier. Valeur donnée : '.var_export($options['notplugin'], $return = true));
            }

            unset($options['notplugin']);
        }

        if (isset($options['has_category']) === true) {
            if (is_bool($options['has_category']) === true) {
                if ($options['has_category'] === true) {
                    $joins[] = 'JOIN categories c ON c.id = s.idcategory';
                } else {
                    $conditions[] = 's.idcategory IS NULL';
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'has_category\' doit être un booléan. Valeur donnée : '.var_export($options['has_category'], $return = true));
            }

            unset($options['has_category']);
        }

        if (isset($options['since']) === true) {
            if ($options['since'] instanceof \DateTime) {
                $conditions[] = '(e.enddate IS NULL OR e.startdate >= :since)';
                $parameters[':since'] = $options['since']->format('Y-m-d\TH:i:s');
            } else {
                throw new \Exception(__METHOD__.': l\'option \'since\' doit être de type DateTime. Valeur donnée : '.var_export($options['since'], $return = true));
            }

            unset($options['since']);
        }

        if (isset($options['finished']) === true) {
            if (is_bool($options['finished']) === true) {
                $parameters[':now'] = date('Y-m-d\TH:i:s');
                if ($options['finished'] === true) {
                    $conditions[] = '(e.enddate IS NOT NULL AND e.enddate <= :now)';
                } else {
                    $conditions[] = 'e.startdate <= :now AND (e.enddate IS NULL OR e.enddate >= :now)';
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'finished\' doit être un booléen. Valeur donnée : '.var_export($options['finished'], $return = true));
            }

            unset($options['finished']);
        }

        if (isset($options['state']) === true) {
            if (isset(State::$STATES[$options['state']]) === true) {
                $conditions[] = 'e.state = :state';
                $parameters[':state'] = $options['state'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'state\' doit être un état valide. Valeur donnée : '.var_export($options['state'], $return = true));
            }

            unset($options['state']);
        }

        if (isset($options['tolerance']) === true) {
            if (is_int($options['tolerance']) === true) {
                if ($options['tolerance'] > 0) {
                    // Ne pas binder 'tolerance', car la requête ne fonctionne pas dans ce cas.
                    $conditions[] = '(e.enddate IS NULL OR (strftime(\'%s\', e.enddate) - strftime(\'%s\', e.startdate) > '.$options['tolerance'].'))';
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'tolerance\' doit être un entier. Valeur donnée : '.var_export($options['tolerance'], $return = true));
            }

            unset($options['tolerance']);
        }

        if (isset($options['type']) === true) {
            if (is_array($options['type']) === false) {
                $options['type'] = array($options['type']);
            }

            $in = array();
            foreach ($options['type'] as $i => $type) {
                if (isset(self::$TYPES[$type]) === true) {
                    $key = ':type'.$i;

                    $in[] = $key;
                    $parameters[$key] = $type;
                } else {
                    throw new \Exception(__METHOD__.': l\'option \'type\' doit être un type d\'évènement valide. Valeur donnée : '.var_export($options['type'], $return = true));
                }
            }

            $conditions[] = 'e.type IN ('.implode(', ', $in).')';
            unset($options['type']);
        }

        // Construit le WHERE.
        if (isset($conditions[0]) === true) {
            $sql_conditions = ' WHERE '.implode(' AND ', $conditions);
        } else {
            $sql_conditions = '';
        }

        // Construit le ORDER BY.
        if (isset($options['sort']) === true) {
            if (is_array($options['sort']) === true) {
                $sql_orders = ' ORDER BY '.implode(', ', $options['sort']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'sort\' doit être de type Array. Valeur donnée : '.var_export($options['sort'], $return = true));
            }

            unset($options['sort']);
        }

        if (isset($sql_orders) === false) {
            $sql_orders = ' ORDER BY e.startdate DESC, e.enddate DESC';
        }

        // Vérifie si toutes les options ont été utilisées.
        foreach ($options as $key => $option) {
            if (in_array($key, array('fetch_column', 'fetch_one'), $strict = true) === true) {
                continue;
            }

            throw new \Exception(__METHOD__.': l\'option \''.$key.'\' n\'a pas été utilisée. Valeur donnée : '.var_export($option, $return = true));
        }

        // Construit la requête.
        $sql = 'SELECT e.id, e.startdate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, e.idservice, s.name AS service_name, s.idplugin'.
            ' FROM events e'.
            ' JOIN events_descriptions ed ON ed.id = e.ideventdescription'.
            ' JOIN services s ON s.id = e.idservice'.
            ' JOIN plugins p ON p.id = s.idplugin AND p.active = 1'.
            ' '.implode(' ', $joins).
            $sql_conditions.
            $sql_orders;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Event');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Détermine si l'évènement en en cours.
     *
     * @param string $datetime Une chaîne date/heure pour l'objet \DateTime.
     *
     * @return boolean
     */
    public function is_now(string $datetime = '') {
        global $LOGGER;

        try {
            $datetime = new \DateTime($datetime);
        } catch (\Exception $exception) {
            $datetime = new \DateTime();
            $LOGGER->info($exception->getMessage());
        }

        $bigger_than_startdate = ($datetime >= $this->startdate);
        $lower_than_enddate = ($this->enddate === null || $datetime <= $this->enddate);

        return $bigger_than_startdate && $lower_than_enddate;
    }

    /**
     * Régénère le fichier ics listant les événements prévus.
     *
     * @return void
     */
    public static function regenerate_ics() {
        global $CFG;

        if ($CFG['ical_enabled'] === '0') {
            return;
        }

        $since = new DateTime();
        $since->setTime(0, 0, 0);
        $since->sub(new DateInterval('P3M'));

        $options = array();
        $options['has_category'] = true;
        $options['plugin'] = PLUGIN_ISOU;
        $options['since'] = $since;
        $options['sort'] = array(
            'e.enddate IS NULL DESC',
            'e.enddate DESC',
            'e.startdate DESC',
        );
        $options['type'] = self::TYPE_SCHEDULED;

        $events = array();
        foreach (self::get_records($options) as $record) {
            $service = Service::get_record(array('id' => $record->idservice));

            $event = new stdClass();
            $event->uid = md5($record->id);
            $event->summary = html_entity_decode($service->name);
            $event->dtstart = $record->startdate->format('Ymd\\THis');
            $event->dtend = null;
            if (empty($record->enddate) === false) {
                $event->dtend = $record->enddate->format('Ymd\\THis');
            }
            $description = str_replace("\r\n", "\\n", $record->description);
            $event->description = strip_tags($description);
            $event->status = 'CONFIRMED';

            $events[] = $event;
        }

        $smarty = new Smarty();
        $smarty->setTemplateDir(PRIVATE_PATH.'/html/');
        $smarty->setCompileDir(PRIVATE_PATH.'/cache/smarty/');

        $smarty->assign('events', $events);
        $smarty->assign('timezone', date_default_timezone_get());

        $data = $smarty->fetch('common/ics.tpl');

        $output_file = PUBLIC_PATH.'/isou.ics';

        // Met à jour le fichier uniquement si le contenu est différent.
        if (is_file($output_file) === false || trim(file_get_contents($output_file)) !== trim($data)) {
            file_put_contents($output_file, $data);
        }
    }

    /**
     * Permet de définir un service.
     *
     * @param string $idservice Identifiant du service à associer à l'évènement.
     * @param array $options_services Tableau indexé des services.
     *
     * @throws \Exception Lève une exception lorsque l'identifiant du service n'est pas valide.
     *
     * @return void
     */
    public function set_service(string $idservice, array $options_services = null) {
        global $DB;

        if ($options_services === null) {
            $options_services = Service::get_records(array('fetch_column' => true, 'plugin' => PLUGIN_ISOU));
        }

        if (is_numeric($idservice) === true) {
            $this->idservice = $idservice;
        } else {
            $service_name = htmlentities(trim($idservice), ENT_NOQUOTES, 'UTF-8');
            $this->idservice = (string) array_search($service_name, $options_services);
        }

        if (isset($options_services[$this->idservice]) === false) {
            throw new \Exception('Le service mis en maintenance n\'est pas valide.');
        }

        // Contrôle si un évènement est déjà en cours pour ce service.
        $sql = 'SELECT e.id, e.type'.
            ' FROM events e'.
            ' WHERE e.id != :id'.
            ' AND e.idservice = :idservice'.
            ' AND (e.enddate IS NULL OR (e.enddate >= :enddate AND e.startdate <= :startdate))';
        $query = $DB->prepare($sql);
        $query->execute(array(':id' => $this->id, ':idservice' => $this->idservice, ':enddate' => STR_TIME, ':startdate' => STR_TIME));
        $event = $query->fetch(\PDO::FETCH_OBJ);

        if ($event !== false) {
            // Lève une exception si un autre évènement est déjà en cours.
            switch ($event->type) {
                case self::TYPE_SCHEDULED:
                    $type = 'prevus';
                    break;
                case self::TYPE_REGULAR:
                    $type = 'reguliers';
                    break;
                case self::TYPE_CLOSED:
                    $type = 'fermes';
                    break;
                case self::TYPE_UNSCHEDULED:
                default:
                    $type = 'imprevus';
            }

            throw new \Exception('Un évènement est déjà en cours pour ce service. Veuillez modifier ou supprimer l\'<a href="'.URL.'/index.php/evenements/'.$type.'/edit/'.$event->id.'"> ancien évènement</a>.');
        }
    }

    /**
     * Permet de définir une période.
     *
     * @param string $period Identifiant de la période à attribuer à l'évènement.
     *
     * @throws \Exception Lève une exception lorsque l'identifiant de la période n'est pas valide.
     *
     * @return void
     */
    public function set_period(string $period) {
        $this->period = $period;

        if (empty($this->period) === true) {
            if ($this->type === self::TYPE_REGULAR) {
                throw new \Exception('Les évènements de type régulier doivent avoir une périodicité.');
            }

            $this->period = self::PERIOD_NONE;
        } else {
            if (isset(self::$PERIODS[$this->period]) === false) {
                throw new \Exception('La périodicité n\'est pas valide.');
            }

            if ($this->type !== self::TYPE_REGULAR) {
                throw new \Exception('Seuls les évènements de type régulier peuvent avoir une périodicité.');
            }

            if ($this->enddate === null) {
                throw new \Exception('Veuillez indiquer une date de fin.');
            }

            $interval = $this->startdate->diff($this->enddate);
            if ($interval->days > 0) {
                throw new \Exception('L\'évènement doit durer moins de 24 heures.');
            }
        }
    }

    /**
     * Permet de définir le type de l'évènement.
     *
     * @param string $type Identifiant du type de l'évènement.
     *
     * @throws \Exception Lève une exception lorsque le type de l'évènement n'est pas valide.
     *
     * @return void
     */
    public function set_type(string $type) {
        $this->type = $type;

        if (isset(self::$TYPES[$this->type]) === false) {
            throw new \Exception('Le type d\'évènement n\'est pas valide.');
        }
    }

    /**
     * Permet de définir la date de début de l'évènement.
     *
     * @param string $date Date au format YYYY-MM-DD.
     * @param string $time Heure au format HH:MM.
     *
     * @throws \Exception Lève une exception lorsque les paramètres ne sont pas valides.
     *
     * @return void
     */
    public function set_startdate(string $date, string $time) {
        try {
            $preg_match_date = preg_match('#^(?P<year>\d{4}).(?P<month>\d{2}).(?P<day>\d{2})$#', $date);
            $preg_match_time = preg_match('#^(?P<hour>\d{2}).(?P<minute>\d{2})$#', $time);

            if ($preg_match_date === 1 && $preg_match_time === 1) {
                $startdate = sprintf('%sT%s:00', $date, $time);
            } else {
                throw new \Exception();
            }

            $datetime = new \DateTime($startdate);
            $this->startdate = $datetime;
        } catch (\Exception $exception) {
            throw new \Exception('La date de début d\'interruption doit être au format AAAA-MM-JJ HH:MM.');
        }
    }

    /**
     * Permet de définir la date de fin de l'évènement.
     *
     * @param string $date Date au format YYYY-MM-DD.
     * @param string $time Heure au format HH:MM.
     *
     * @throws \Exception Lève une exception lorsque les paramètres ne sont pas valides.
     *
     * @return void
     */
    public function set_enddate(string $date, string $time) {
        if (empty($date) === true || empty($time) === true) {
            $this->enddate = null;
        } else {
            try {
                $preg_match_date = preg_match('#^(?P<year>\d{4}).(?P<month>\d{2}).(?P<day>\d{2})$#', $date);
                $preg_match_time = preg_match('#^(?P<hour>\d{2}).(?P<minute>\d{2})$#', $time);

                if ($preg_match_date === 1 && $preg_match_time === 1) {
                    $enddate = sprintf('%sT%s:00', $date, $time);
                } else {
                    throw new \Exception();
                }

                $datetime = new \DateTime($enddate);
                $this->enddate = $datetime;
            } catch (\Exception $exception) {
                throw new \Exception('La date de fin d\'interruption doit être au format AAAA-MM-JJ HH:MM.');
            }

            if ($this->startdate >= $this->enddate) {
                throw new \Exception('La date de début doit être inférieure à la date de fin.');
            }
        }

        if ($this->type === self::TYPE_REGULAR) {
            // Note: on laisse la possibilité à l'utilisateur de saisir des journées différentes, notamment dans le cas où la date d'interruption régulière aurait lieu de 23h à 1h.

            if ($this->enddate === null) {
                throw new \Exception('Une date de fin est requise pour un évènement régulier.');
            }
        }
    }

    /**
     * Permet de définir l'état de l'évènement.
     *
     * @param string $state Etat à attribuer à l'évènement.
     * @param array $options_states Tableau indexé des états.
     *
     * @throws \Exception Lève une exception lorsque l'identifiant de l'état n'est pas valide.
     *
     * @return void
     */
    public function set_state(string $state, array $options_states = null) {
        $this->state = $state;

        if ($options_states === null) {
            $options_states = State::$STATES;
        }

        if (isset($options_states[$this->state]) === false) {
            throw new \Exception('L\'état du service a une valeur incorrecte.');
        }

        if ($this->type === self::TYPE_CLOSED) {
            if ($this->state !== State::CLOSED) {
                throw new \Exception('L\'état du service doit être positionné sur fermé pour un évènement de fermeture');
            }
        }
    }

    /**
     * Permet de définir la description d'un évènement.
     *
     * @param string $description Contient le texte de la description.
     * @param boolean $autogen Indique si la description a été saisie par un utilisateur ou automatiquement générée par Isou.
     *
     * @return void
     */
    public function set_description(string $description = '', bool $autogen = false) {
        $event_description = Event_Description::get_record(array('description' => $description, 'autogen' => $autogen));
        if ($event_description === false) {
            $event_description = new Event_Description();
            $event_description->description = $description;
            $event_description->autogen = intval($autogen);
        }

        $this->ideventdescription = $event_description->id;
        $this->description = $event_description;
    }

    /**
     * Enregistre l'objet Event et Event_Description en base de données.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors de l'enregistrement.
     *
     * @return void
     */
    public function save() {
        global $DB, $LOGGER;

        if ($DB->inTransaction() === false) {
            $LOGGER->warning('Il est recommandé de démarrer une transaction lorsqu\'on enregistre un évènement.');
        }

        if ($this->description === null) {
            $this->set_description();
        }

        if (isset($this->description->id) === true && empty($this->description->id) === true) {
            $this->description->save();
            $this->ideventdescription = $DB->lastInsertId();
        }

        if ($this->enddate === null) {
            $enddate = null;
        } else {
            $enddate = $this->enddate->format('Y-m-d\TH:i:s');
        }

        $params = array(
            ':startdate' => $this->startdate->format('Y-m-d\TH:i:s'),
            ':enddate' => $enddate,
            ':state' => $this->state,
            ':type' => $this->type,
            ':period' => $this->period,
            ':ideventdescription' => $this->ideventdescription,
            ':idservice' => $this->idservice,
        );

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO events(startdate, enddate, state, type, period, ideventdescription, idservice) VALUES(:startdate, :enddate, :state, :type, :period, :ideventdescription, :idservice)';
        } else {
            $sql = 'UPDATE events SET startdate=:startdate, enddate=:enddate, state=:state, type=:type, period=:period, ideventdescription=:ideventdescription, idservice=:idservice WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
            if (empty($this->id) === true) {
                $this->id = $DB->lastInsertId();
            }

            if ($this->type === self::TYPE_SCHEDULED) {
                // On regénère le fichier isou.ics.
                self::regenerate_ics();
            }
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de l\'évènement.');
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

        $sql = 'DELETE FROM events WHERE id = :id';
        $query = $DB->prepare($sql);

        if ($query->execute(array(':id' => $this->id)) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de l\'évènement.');
        }
    }

    /**
     * Clôture l'évènement.
     *
     * @return boolean Retourne true si l'évènement a pu être clôturer.
     */
    public function close() {
        global $DB, $LOGGER;

        $sql = 'UPDATE events SET enddate=:enddate WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':enddate' => STR_TIME, ':id' => $this->id)) === true) {
            $this->enddate = new \DateTime(STR_TIME);
            return true;
        } else {
            $LOGGER->error(implode(', ', $query->errorInfo()));
            return false;
        }
    }
}
