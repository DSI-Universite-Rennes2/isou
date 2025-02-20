<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

use Exception;
use stdClass;

/**
 * Classe décrivant un module.
 */
class Plugin {
    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Nom du module.
     *
     * @var string
     */
    public $name;

    /**
     * Nom de code unique du module.
     *
     * @var string
     */
    public $codename;

    /**
     * Type du module (authentication, monitoring ou view).
     *
     * @var string
     */
    public $type;

    /**
     * Témoin indiquant si le module est activé. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $active;

    /**
     * Numéro de version du module.
     *
     * @var string
     */
    public $version;

    /**
     * Paramètres du module.
     *
     * @var stdClass
     */
    public $settings;

    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        $this->settings = new stdClass();
        $this->set_settings();
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Plugin|false
     */
    public static function get_record(array $options = array()) {
        if (isset($options['id']) === false && isset($options['codename']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] ou $options[\'codename\'] est requis.');
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
     * @return Plugin[]|Plugin|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
                $conditions[] = 'p.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['codename']) === true) {
            if (is_string($options['codename']) === true) {
                $conditions[] = 'p.codename = :codename';
                $parameters[':codename'] = $options['codename'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'codename\' doit être une chaine de caractères. Valeur donnée : '.var_export($options['codename'], $return = true));
            }

            unset($options['codename']);
        }

        if (isset($options['type']) === true) {
            if (is_string($options['type']) === true) {
                $conditions[] = 'p.type = :type';
                $parameters[':type'] = $options['type'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'type\' doit être une chaine de caractères. Valeur donnée : '.var_export($options['type'], $return = true));
            }

            unset($options['type']);
        }

        if (isset($options['active']) === true) {
            if (is_bool($options['active']) === true) {
                $conditions[] = 'p.active = :active';
                $parameters[':active'] = intval($options['active']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'active\' doit être un booléan. Valeur donnée : '.var_export($options['active'], $return = true));
            }

            unset($options['active']);
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
        if (isset($options['fetch_column']) === true) {
            $sql = 'SELECT p.id, p.name'.
                ' FROM plugins p'.
                $sql_conditions.
                ' ORDER BY UPPER(p.name)';

            $query = $DB->prepare($sql);
            $query->execute($parameters);

            return $query->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }

        $sql = 'SELECT p.id, p.name, p.codename, p.type, p.active, p.version'.
                ' FROM plugins p'.
                ' '.$sql_conditions.
                ' ORDER BY UPPER(p.name)';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Plugin');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Définit les paramètres du module.
     *
     * @return void
     */
    public function set_settings() {
        global $DB, $LOGGER;

        $sql = 'SELECT s.id, s.key, s.value, s.type, s.idplugin'.
                ' FROM plugins_settings s'.
                ' WHERE s.idplugin = :idplugin';
        $query = $DB->prepare($sql);
        $query->execute(array(':idplugin' => $this->id));

        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $setting) {
            $this->settings->{$setting->key} = self::decode_settings($setting->value, $setting->type);
        }
    }

    /**
     * Installe le module en base de données.
     *
     * TODO: retourner un truc.
     *
     * @return void
     */
    public function install() {
        echo 'Installation du plugin '.$this->name.' ('.$this->version.')'.PHP_EOL;

        $this->save();
    }

    /**
     * Décode une valeur enregistrée en base de données.
     *
     * @param mixed $value Valeur à décoder.
     * @param string $type Type initial de la valeur.
     *
     * @return mixed
     */
    public static function decode_settings(mixed $value, string $type) {
        switch ($type) {
            case 'array':
                return json_decode($value);
            case 'boolean':
                return boolval($value);
            case 'datetime':
                try {
                    $datetime = new \DateTime($value);
                } catch (Exception $exception) {
                    $datetime = new \DateTime('1970-01-01');
                }
                return $datetime;
            case 'integer':
                return intval($value);
            case 'string':
            default:
                return $value;
        }
    }

    /**
     * Encode une valeur pour l'enregistrer en base de données.
     *
     * @param mixed $value Valeur à convertir en type texte.
     *
     * @return array
     */
    public static function encode_settings(mixed $value) {
        $settings = array();
        $settings[0] = $value;

        if (is_array($value) === true) {
            $settings[0] = json_encode($value);
            $settings[1] = 'array';
        } elseif (is_bool($value) === true) {
            $settings[0] = intval($value);
            $settings[1] = 'boolean';
        } elseif ($value instanceof \DateTime) {
            $settings[0] = $value->format('Y-m-d\TH:i:s');
            $settings[1] = 'datetime';
        } elseif (is_integer($value) === true) {
            $settings[1] = 'integer';
        } else {
            $settings[1] = 'string';
        }

        return $settings;
    }

    /**
     * Insère les paramètres en base de données.
     *
     * TODO: retourner un truc.
     *
     * @return void
     */
    public function install_settings() {
        global $DB;

        $sql = 'INSERT INTO plugins_settings(key, value, type, idplugin) VALUES(:key, :value, :type, :idplugin)';
        $query = $DB->prepare($sql);

        $settings = (array) $this->settings;
        foreach ($settings as $key => $value) {
            list($value, $type) = self::encode_settings($value);

            $params = array();
            $params[':key'] = $key;
            $params[':value'] = $value;
            $params[':type'] = $type;
            $params[':idplugin'] = $this->id;

            $query->execute($params);
        }
    }

    /**
     * Enregistre l'objet en base de données.
     *
     * TODO: retourner un truc.
     *
     * @return void
     */
    public function save() {
        global $DB;

        $params = array();
        $params[':name'] = $this->name;
        $params[':codename'] = $this->codename;
        $params[':version'] = $this->version;

        if (isset($this->id) === false) {
            // Install.
            $params[':type'] = $this->type;
            $params[':active'] = 0;

            $sql = 'INSERT INTO plugins(name, codename, type, active, version)'.
                ' VALUES(:name, :codename, :type, :active, :version)';
        } else {
            $params[':active'] = $this->active;

            $sql = 'UPDATE plugins SET name = :name, active = :active, version = :version WHERE codename = :codename';
        }

        $query = $DB->prepare($sql);
        if ($query->execute($params) === true) {
            if (isset($this->id) === false) {
                $this->id = $DB->lastInsertId();
            }
        }
    }

    /**
     * Met à jour le module en base de données.
     *
     * TODO: retourner un truc.
     *
     * @return void
     */
    public function update() {
        echo 'Mise à jour du plugin '.$this->name.' ('.$this->version.')'.PHP_EOL;

        $this->save();
    }

    /**
     * Met à jour les paramètres du module en base de données.
     *
     * TODO: retourner un truc.
     *
     * @param boolean $overwrite Témoin indiquant si les paramètres doivent être écrasés. Valeurs possibles '0' ou '1'.
     *
     * @return void
     */
    public function update_settings(bool $overwrite = false) {
        global $DB;

        $settings = (array) $this->settings;
        foreach ($settings as $key => $value) {
            $sql = 'SELECT id FROM plugins_settings WHERE key = :key AND idplugin = :idplugin';
            $query = $DB->prepare($sql);
            $query->execute(array(':key' => $key, ':idplugin' => $this->id));
            $setting = $query->fetch();

            if ($setting === false) {
                $sql = 'INSERT INTO plugins_settings(key, value, type, idplugin) VALUES(:key, :value, :type, :idplugin)';
            } elseif ($overwrite === true) {
                $sql = 'UPDATE plugins_settings SET value = :value, type = :type WHERE key = :key AND idplugin = :idplugin';
            } else {
                continue;
            }

            $query = $DB->prepare($sql);

            list($value, $type) = self::encode_settings($value);

            $params = array();
            $params[':key'] = $key;
            $params[':value'] = $value;
            $params[':type'] = $type;
            $params[':idplugin'] = $this->id;

            $query->execute($params);
        }
    }
}
