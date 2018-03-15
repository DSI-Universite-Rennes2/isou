<?php

namespace UniversiteRennes2\Isou;

class Event{
    const PERIOD_NONE = '0';
    const PERIOD_DAILY = '86400';
    const PERIOD_WEEKLY = '604800';

    const TYPE_UNSCHEDULED = '0';
    const TYPE_SCHEDULED = '1';
    const TYPE_REGULAR = '2';
    const TYPE_CLOSED = '3';

    public $id;
    public $startdate;
    public $enddate;
    public $state;
    public $type;
    public $period;
    public $ideventdescription;
    public $description;
    public $idservice;

    public static $TYPES = array(
        self::TYPE_SCHEDULED => 'Évènement prévu',
        self::TYPE_UNSCHEDULED => 'Évènement imprévu',
        self::TYPE_REGULAR => 'Évènement régulier',
        self::TYPE_CLOSED => 'Service fermé',
        );
    public static $PERIODS = array(
        self::PERIOD_NONE => 'Aucune',
        self::PERIOD_DAILY => 'Tous les jours',
        self::PERIOD_WEEKLY => 'Toutes les semaines',
        );

    public function __construct() {
        if (isset($this->id)) {
            // PDO instance
            try {
                $this->startdate = new \DateTime($this->startdate);
                if ($this->enddate !== null) {
                    $this->enddate = new \DateTime($this->enddate);
                }
            } catch (Exception $exception) {
                $this->startdate = new \DateTime();
                $this->enddate = new \DateTime();
            }

            if (empty($this->period)) {
                $this->period = self::PERIOD_NONE;
            }
        } else {
            // manual instance
            $this->id = 0;
            $this->startdate = new \DateTime();
            $this->enddate = null;
            $this->state = State::CRITICAL;
            $this->type = self::TYPE_SCHEDULED;
            $this->period = '0';
            $this->ideventdescription = 1;
            $this->description = null;
            $this->idservice = 0;
        }
    }

    public function set_service($idservice, $options_services = null) {
        global $DB;

        $this->idservice = $idservice;

        if ($options_services === null) {
            require_once PRIVATE_PATH.'/libs/services.php';

            $options_services = get_isou_services_sorted_by_idtype();
        }

        if (!isset($options_services[$this->idservice])) {
            throw new \Exception('Le service mis en maintenance n\'est pas valide.');
        } else {
            $sql = "SELECT COUNT(E.id) AS total".
                    " FROM events E".
                " WHERE E.id != ?".
                " AND E.idservice = ?".
                " AND (E.enddate IS NULL OR (E.enddate >= ? AND E.startdate <= ?))";
            $query = $DB->prepare($sql);
            $query->execute(array($this->id, $this->idservice, STR_TIME, STR_TIME));
            $count = $query->fetch(\PDO::FETCH_OBJ);
            if ($count->total !== '0') {
                throw new \Exception('Un évènement est déjà en cours pour ce service. Veuillez modifier ou supprimer l\'ancien évènement.');
            }
        }
    }

    public function set_period($period) {
        $this->period = $period;

        if (empty($this->period)) {
            $this->period = self::PERIOD_NONE;
        } else {
            if (!isset(self::$PERIODS[$this->period])) {
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

    public function set_type($type) {
        $this->type = $type;

        if (!isset(self::$TYPES[$this->type])) {
            throw new \Exception('Le type d\'opération n\'est pas valide.');
        }
    }

    public function set_startdate($date, $time) {
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

    public function set_enddate($date, $time) {
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

            if (($this->startdate < $this->enddate) === false) {
                throw new \Exception('La date de début doit être inférieure à la date de fin.');
            }
        }
    }

    public function set_state($state, $options_states = null) {
        $this->state = $state;

        if ($options_states === null) {
            $options_states = State::$STATES;
        }

        if (!isset($options_states[$this->state])) {
            throw new \Exception('L\'état du service a une valeur incorrecte.');
        }
    }

    public function save() {
        global $DB, $LOGGER;

        if ($this->enddate === null) {
            $enddate = null;
        } else {
            $enddate = $this->enddate->format('Y-m-d\TH:i:s');
        }

        $params = array(
        $this->startdate->format('Y-m-d\TH:i:s'),
        $enddate,
        $this->state,
        $this->type,
        $this->period,
        $this->ideventdescription,
        $this->idservice,
        );

        if ($this->id === 0) {
            $sql = "INSERT INTO events(startdate, enddate, state, type, period, ideventdescription, idservice) VALUES(?,?,?,?,?,?,?)";
        } else {
            $sql = "UPDATE events SET startdate=?, enddate=?, state=?, type=?, period=?, ideventdescription=?, idservice=? WHERE id=?";
            $params[] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            if ($this->id === 0) {
                $this->id = $DB->lastInsertId();
            }
        } else {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de l\'évènement.');
        }
    }

    public function delete() {
        global $DB, $LOGGER;

        $sql = "DELETE FROM events WHERE id=?";
        $query = $DB->prepare($sql);

        if ($query->execute(array($this->id)) === false) {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de l\'évènement.');
        }
    }

    public function close() {
        global $DB, $LOGGER;

        $sql = "UPDATE events SET enddate=? WHERE id=?";
        $query = $DB->prepare($sql);
        if ($query->execute(array(STR_TIME, $this->id))) {
            $this->enddate = new \DateTime(STR_TIME);
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function close_all_other_events() {
        global $DB, $LOGGER;

        $sql = "UPDATE events SET enddate=? WHERE id != ? AND idservice=? AND startdate <= ? AND (enddate IS NULL OR enddate >= ?)";
        $query = $DB->prepare($sql);
        if ($query->execute(array(STR_TIME, $this->id, $this->idservice, STR_TIME, STR_TIME))) {
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function set_description($description = null, $autogen = 0) {
        global $DB, $LOGGER;

        if ($description !== null) {
            $this->description = $description;
        }
        $description = get_event_description_by_content($this->description);

        if ($description === false) {
            $sql = "INSERT INTO events_descriptions(description, autogen) VALUES(?,?)";
            $query = $DB->prepare($sql);
            if ($query->execute(array($this->description, $autogen))) {
                $this->ideventdescription = $DB->lastInsertId();
                $this->description = $description;
            } else {
                // log db errors
                $LOGGER->addError(implode(', ', $query->errorInfo()));
                return false;
            }
        } else {
            $this->ideventdescription = $description->ideventdescription;
            $this->description = $description;
        }

        return true;
    }

    public function __toString() {
        $str = '';

        if ($this->state === State::CLOSED) {
            $str = 'Service fermé depuis le '.strftime('%A %d %B %Y', $this->startdate->getTimestamp()).'.';
            if ($this->enddate !== null) {
                $str .= ' Réouverture le '.strftime('%A %d %B %Y', $this->enddate->getTimestamp()).'.';
            }
        } elseif (empty($this->period) === false) {
            $starttime = $this->startdate->format('H\hi');
            $endtime = $this->enddate->format('H\hi');

            switch ($this->period) {
                case self::PERIOD_WEEKLY:
                    $str = 'Tous les '.strftime('%A', $this->startdate->getTimestamp()).' de '.$starttime.' à '.$endtime.'.';
                    break;
                case self::PERIOD_DAILY:
                default:
                    $str = 'Tous les jours de '.$starttime.' à '.$endtime.'.';
            }
        } else {
            $starttime = $this->startdate->format('H\hi');
            $startday = strftime('%A %d %B', $this->startdate->getTimestamp());

            if ($this->type === self::TYPE_SCHEDULED) {
                $type = 'en maintenance';
            } else {
                $type = 'perturbé';
            }

            if ($this->startdate->getTimestamp() > TIME) {
                // Évènement futur.
                if ($this->enddate === null) {
                    $str = 'Le service sera '.$type.' le '.$startday.' à partir de '.$starttime.'.';
                } else {
                    $endtime = $this->enddate->format('H\hi');
                    $endday = strftime('%A %d %B', $this->enddate->getTimestamp());
                    $str = 'Le service sera '.$type.' du '.$startday.' '.$starttime.' au '.$endday.' '.$endtime.'.';
                }
            } elseif ($this->enddate !== null && $this->enddate->getTimestamp() < TIME) {
                $endtime = $this->enddate->format('H\hi');

                // Évènement passé.
                if (strftime('%A%d%B', $this->startdate->getTimestamp()) === strftime('%A%d%B', $this->enddate->getTimestamp())) {
                    // Évènement qui s'est déroulé sur une journée.
                    $str = 'Le service a été '.$type.' le '.$startday.' de '.$starttime.' à '.$endtime.'.';
                } else {
                    // Évènement qui s'est déroulé sur plusieurs journées.
                    $endday = strftime('%A %d %B', $this->enddate->getTimestamp());
                    $str = 'Le service a été '.$type.' du '.$startday.' '.$starttime.' au '.$endday.' '.$endtime.'.';
                }
            } else {
                // Évènement en cours.
                if (strftime('%A%d%B', $this->startdate->getTimestamp()) === strftime('%A%d%B')) {
                    $str = 'Le service est '.$type.' depuis '.$starttime.'.';
                } else {
                    $str = 'Le service est '.$type.' depuis le '.$startday.' '.$starttime.'.';
                }
            }
        }

        return $str;
    }

    /**
      * @desc   Destruct instance
      */
    public function __destruct() {
        // object destructed
    }
}
