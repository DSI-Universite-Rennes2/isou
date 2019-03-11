<?php

namespace UniversiteRennes2\Isou;

class State {
    const OK = '0';
    const GREEN = '0';

    const WARNING = '1';
    const ORANGE = '1';

    const CRITICAL = '2';
    const RED = '2';

    const UNKNOWN = '3';
    const BLUE = '3';

    const CLOSED = '4';
    const WHITE = '4';

    public $id;
    public $name;
    public $title;
    public $alternate_text;
    public $image;

    public static $STATES = array(
        self::OK => 'Fonctionne',
        self::WARNING => 'Instable',
        self::CRITICAL => 'Indisponible',
        self::UNKNOWN => 'Indéterminé',
        self::CLOSED => 'Fermé',
    );

    public function __construct() {
        if (isset($this->id) === false) {
            // Instance manuelle.
            $this->id = 0;
            $this->name = '';
            $this->title = '';
            $this->alternate_text = '';
            $this->image = '';
        }
    }

    public function __tostring() {
        return $this->get_flag_html_renderer();
    }

    public function get_flag_html_renderer() {
        global $CFG;

        return '<img src="'.URL.'/themes/'.$CFG['theme'].'/images/'.$this->image.'" alt="'.$this->alternate_text.'" width="16px" height="16px" />';
    }

    public static function get_record($options = array()) {
        if (isset($options['id']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] est requis.');
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
                $conditions[] = 's.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
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
        $sql = 'SELECT s.id, s.name, s.title, s.alternate_text, s.image'.
           ' FROM states s'.
            $sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\State');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }
}
