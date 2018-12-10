<?php

namespace UniversiteRennes2\Isou;

class Event {
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

    // TODO: à simplifier.
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

            if (empty($this->period)) {
                $this->period = self::PERIOD_NONE;
            }
        } else {
            // Instance manuelle.
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

    public function __tostring() {
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

    public static function get_record($options = array()) {
        if (isset($options['id']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] est requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
      * @param array $options Array in format:
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
      * @return array of UniversiteRennes2\Isou\Events
      */
    public static function get_records($options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcours les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
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
            } else if (isset($options['enddate_between'][1]) === false) {
                throw new \Exception(__METHOD__.': l\'option \'enddate_between\' doit contenir 2 éléments de type DateTime. Valeur donnée : '.var_export($options['enddate_between'], $return = true));
            } else {
                if (($options['enddate_between'][0] instanceof \DateTime) === false) {
                    throw new \Exception('Le premier élément de \'option \'enddate_between\' doit être de type DateTime. Valeur donnée : '.var_export($options['enddate_between'][0], $return = true));
                } else if (($options['enddate_between'][1] instanceof \DateTime) === false) {
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
            if (ctype_digit($options['idservice']) === true) {
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
                    $conditions[] = 'e.period IS NOT NULL';
                } else {
                    $conditions[] = 'e.period IS NULL';
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'regular\' doit être un booléen. Valeur donnée : '.var_export($options['regular'], $return = true));
            }

            unset($options['regular']);
        }

        if (isset($options['plugin']) === true) {
            if (ctype_digit($options['plugin']) === true) {
                $conditions[] = 's.idplugin = :idplugin';
                $parameters[':idplugin'] = $options['plugin'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'plugin\' doit être un entier. Valeur donnée : '.var_export($options['plugin'], $return = true));
            }

            unset($options['plugin']);
        }

        if (isset($options['notplugin']) === true) {
            if (ctype_digit($options['notplugin']) === true) {
                $conditions[] = 's.idplugin != :idplugin';
                $parameters[':idplugin'] = $options['notplugin'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'notplugin\' doit être un entier. Valeur donnée : '.var_export($options['notplugin'], $return = true));
            }

            unset($options['notplugin']);
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
                if ($options['finished'] === true) {
                    $conditions[] = 'e.enddate IS NOT NULL';
                } else {
                    $conditions[] = 'e.enddate IS NULL';
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
            if (ctype_digit($options['tolerance']) === true) {
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
            if (isset(Event::$TYPES[$options['type']]) === true) {
                $conditions[] = 'e.type = :type';
                $parameters[':type'] = $options['type'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'type\' doit être un type d\'évènement valide. Valeur donnée : '.var_export($options['type'], $return = true));
            }

            unset($options['type']);
        }

        // Construis le WHERE.
        if (isset($conditions[0]) === true) {
            $sql_conditions = ' WHERE '.implode(' AND ', $conditions);
        } else {
            $sql_conditions = '';
        }

        // Construis le ORDER BY.
        if (isset($options['sort']) === true) {
            if (is_array($options['sort']) === true) {
                $sql_orders = ' ORDER BY '.implode(', ', $options['sort']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'sort\' doit être de type Array. Valeur donnée : '.var_export($options['sort'], $return = true));
            }

            unset($options['sort']);
        }

        if (isset($sql_orders) === false) {
            $sql_orders = ' ORDER BY e.startdate, e.enddate';
        }

        // Vérifie si toutes les options ont été utilisées.
        foreach ($options as $key => $option) {
            if (in_array($key, array('fetch_column', 'fetch_one'), $strict = true) === true) {
                continue;
            }

            throw new \Exception(__METHOD__.': l\'option \''.$key.'\' n\'a pas été utilisée. Valeur donnée : '.var_export($option, $return = true));
        }

        // Construis la requête.
        $sql = 'SELECT e.id, e.startdate, e.enddate, e.state, e.type, e.period, e.ideventdescription, ed.description, e.idservice, s.name AS service_name'.
            ' FROM events e'.
            ' JOIN events_descriptions ed ON ed.id = e.ideventdescription'.
            ' JOIN services s ON s.id = e.idservice'.
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

    public function is_now($datetime = null) {
        global $LOGGER;

        try {
            $datetime = new \DateTime($datetime);
        } catch (\Exception $exception) {
            $datetime = new \DateTime();
            $LOGGER->addInfo($exception->getMessage());
        }

        $bigger_than_startdate = ($datetime >= $this->startdate);
        $lower_than_enddate = ($this->enddate === null || $datetime <= $this->enddate);

        return $bigger_than_startdate && $lower_than_enddate;
    }

    public function set_service($idservice, $options_services = null) {
        global $DB;

        $this->idservice = $idservice;

        if ($options_services === null) {
            $options_services = Service::get_records(array('fetch_column' => true, 'plugin' => PLUGIN_ISOU));
        }

        if (isset($options_services[$this->idservice]) === false) {
            throw new \Exception('Le service mis en maintenance n\'est pas valide.');
        }

        $sql = 'SELECT COUNT(e.id) AS total'.
            ' FROM events e'.
            ' WHERE e.id != :id'.
            ' AND e.idservice = :idservice'.
            ' AND (e.enddate IS NULL OR (e.enddate >= :enddate AND e.startdate <= :startdate))';
        $query = $DB->prepare($sql);
        $query->execute(array(':id' => $this->id, ':idservice' => $this->idservice, ':enddate' => STR_TIME, ':startdate' => STR_TIME));
        $count = $query->fetch(\PDO::FETCH_OBJ);
        if ($count !== false && $count->total !== '0') {
            throw new \Exception('Un évènement est déjà en cours pour ce service. Veuillez modifier ou supprimer l\'ancien évènement.');
        }
    }

    public function set_period($period) {
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

    public function set_type($type) {
        $this->type = $type;

        if (isset(self::$TYPES[$this->type]) === false) {
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

    public function set_state($state, $options_states = null) {
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

    public function save() {
        global $DB, $LOGGER;

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

        if ($this->id === 0) {
            $sql = 'INSERT INTO events(startdate, enddate, state, type, period, ideventdescription, idservice) VALUES(:startdate, :enddate, :state, :type, :period, :ideventdescription, :idservice)';
        } else {
            $sql = 'UPDATE events SET startdate=:startdate, enddate=:enddate, state=:state, type=:type, period=:period, ideventdescription=:ideventdescription, idservice=:idservice WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            if ($this->id === 0) {
                $this->id = $DB->lastInsertId();
            }
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de l\'évènement.');
        }
    }

    public function delete() {
        global $DB, $LOGGER;

        $sql = 'DELETE FROM events WHERE id = :id';
        $query = $DB->prepare($sql);

        if ($query->execute(array(':id' => $this->id)) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de l\'évènement.');
        }
    }

    public function close() {
        global $DB, $LOGGER;

        $sql = 'UPDATE events SET enddate=:enddate WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':enddate' => STR_TIME, ':id' => $this->id))) {
            $this->enddate = new \DateTime(STR_TIME);
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function set_description($description = null, $autogen = 0) {
        global $DB, $LOGGER;

        if ($description === null) {
            $description = $this->description;
        }

        $event_description = Event_Description::get_record(array('description' => $description, 'autogen' => false));
        if ($event_description === false) {
            $event_description = new Event_Description();
            $event_description->description = $description;
            $event_description->autogen = $autogen;

            $event_description->save();
        }

        $this->ideventdescription = $event_description->id;
        $this->description = $event_description;

        return true;
    }
}
