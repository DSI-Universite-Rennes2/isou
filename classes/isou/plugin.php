<?php

namespace UniversiteRennes2\Isou;

class Plugin {
    public $id;
    public $name;
    public $codename;
    public $active;
    public $version;
    public $settings;

    public function __construct() {
        $this->settings = new \stdClass();
        $this->set_settings();
    }

    public static function get_plugin($options = array()) {
        $options['single'] = true;

        return self::get_plugins($options);
    }

    public static function get_plugins($options = array()) {
        global $DB, $LOGGER;

        $params = array();
        $conditions = array();

        if (isset($options['id']) === true) {
            $conditions[] = 'p.id = :id';
            $params[':id'] = $options['id'];
        }

        if (isset($options['codename']) === true) {
            $conditions[] = 'p.codename = :codename';
            $params[':codename'] = $options['codename'];
        }

        if (isset($options['active']) === true) {
            if (is_bool($options['active'])) {
                $conditions[] = 'p.active = :active';
                $params[':active'] = intval($options['active']);
            } else {
                $LOGGER->addInfo('L\'option \'active\' doit être un booléan.', array('value', $options['active']));
            }
        }

        if (isset($conditions[0]) === true) {
            $sql_condition = ' WHERE '.implode(' AND ', $conditions);
        }else{
            $sql_condition = '';
        }

        $sql = 'SELECT p.id, p.name, p.codename, p.active, p.version'.
                ' FROM plugins p'.
                ' '.$sql_condition.
                ' ORDER BY UPPER(p.name)';
        $query = $DB->prepare($sql);
        $query->execute($params);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Plugin');

        $records = $query->fetchAll();

        if (isset($options['single']) === true) {
            if (isset($records[0]) === true) {
                if (isset($records[1]) === true) {
                    $LOGGER->addInfo('Plusieurs valeurs ont été retournées par la méthode '.__METHOD__.' avec l\'option "single".');
                }

                return $records[0];
            }else{
                return false;
            }
        }

        return $records;
    }

    public function set_settings() {
        global $DB, $LOGGER;

        $sql = 'SELECT s.id, s.key, s.value, s.type, s.idplugin'.
                ' FROM plugins_settings s'.
                ' WHERE s.idplugin = :idplugin';
        $query = $DB->prepare($sql);
        $query->execute(array(':idplugin' => $this->id));

        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $setting) {
            switch ($setting->type) {
                case 'array':
                    $this->settings->{$setting->key} = json_decode($setting->value);
                    break;
                case 'datetime':
                    try {
                        $this->settings->{$setting->key} = new DateTime($setting->value);
                    } catch (Exception $exception) {
                        $this->settings->{$setting->key} = new DateTime('1970-01-01');
                    }
                    break;
                case 'string':
                default:
                    $this->settings->{$setting->key} = $setting->value;
            }

        }
    }

    public function install() {
        echo 'Installation du plugin '.$this->name.' ('.$this->version.')'.PHP_EOL;

        return $this->save();
    }

    public function install_settings() {
        global $DB;

        $sql = "INSERT INTO plugins_settings(key, value, type, idplugin) VALUES(:key, :value, :type, :idplugin)";
        $query = $DB->prepare($sql);

        $settings = (array) $this->settings;
        foreach ($settings as $key => $value) {
            $params = array();
            $params[':key'] = $key;
            $params[':value'] = $value;
            $params[':type'] = 'string';
            $params[':idplugin'] = $this->id;

            $query->execute($params);
        }
    }

    public function save() {
        global $DB;

        $params = array();
        $params[':name'] = $this->name;
        $params[':codename'] = $this->codename;
        $params[':version'] = $this->version;

        if (isset($this->id) === false) {
            // Install.
            $params[':active'] = 0;

            $sql = "INSERT INTO plugins(name, codename, active, version)".
                " VALUES(:name, :codename, :active, :version)";
        } else {
            $params[':active'] = $this->active;

            $sql = "UPDATE plugins SET name = :name, active = :active, version = :version WHERE codename = :codename";
        }

        $query = $DB->prepare($sql);
        if ($query->execute($params) === true) {
            if (isset($this->id) === false) {
                $this->id = $DB->lastInsertId();
            }
        }
    }

    public function update() {
        // throw new \Exception('La méthode '.__METHOD__.' n\'est pas implémentée.');
        echo 'Mise à jour du plugin '.$this->name.' ('.$this->version.')'.PHP_EOL;

        return $this->save();
    }

    public function update_settings($overwrite = false) {
        global $DB;

        $settings = (array) $this->settings;
        foreach ($settings as $key => $value) {

            $sql = "SELECT id FROM plugins_settings WHERE key = :key AND idplugin = :idplugin";
            $query = $DB->prepare($sql);
            $query->execute(array(':key' => $key, ':idplugin' => $this->id));
            $setting = $query->fetch();

            if ($setting === false) {
                $sql = "INSERT INTO plugins_settings(key, value, type, idplugin) VALUES(:key, :value, :type, :idplugin)";
            } else if ($overwrite === true) {
                $sql = "UPDATE plugins_settings SET value = :value WHERE key = :key AND idplugin = :idplugin";
            } else {
                continue;
            }

            $query = $DB->prepare($sql);

            $params = array();
            $params[':key'] = $key;
            $params[':value'] = $value;
            $params[':type'] = 'string';
            $params[':idplugin'] = $this->id;

            $query->execute($params);
        }
    }
}
