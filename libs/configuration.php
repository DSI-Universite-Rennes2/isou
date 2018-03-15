<?php

use UniversiteRennes2\Isou\Plugin;

/**
  * Détermine si l'application doit être mise à jour.
  *
  * @return boolean Retourne true si une mise à jour est disponible.
  */
function has_new_version() {
    global $CFG;

    return CURRENT_VERSION !== $CFG['version'];
}

function get_configurations() {
    global $DB;

    $sql = "SELECT key, value, type FROM configuration";
    $query = $DB->prepare($sql);

    if ($query === false) {
        // Try to query old database scheme.
        $sql = "SELECT key, value, 'string' AS type FROM configuration";
        $query = $DB->prepare($sql);
    }

    $configurations = array();
    if ($query !== false) {
        $query->execute();
        while ($config = $query->fetch(PDO::FETCH_OBJ)) {
            switch ($config->type) {
                case 'array':
                    $configurations[$config->key] = json_decode($config->value);
                    break;
                case 'datetime':
                    try {
                        $configurations[$config->key] = new DateTime($config->value);
                    } catch (Exception $exception) {
                        $configurations[$config->key] = new DateTime('1970-01-01');
                    }
                    break;
                case 'string':
                default:
                    $configurations[$config->key] = $config->value;
            }
        }
    }

    return $configurations;
}

function get_plugins() {
    $plugins = Plugin::get_plugins();
    foreach ($plugins as $plugin) {
        define('PLUGIN_'.strtoupper($plugin->codename), $plugin->id);
    }

    return $plugins;
}

function set_configuration($key, $value, $field = null) {
    global $DB;

    $sql = "UPDATE configuration SET value=? WHERE key=?";
    $query = $DB->prepare($sql);
    if ($query->execute(array($value, $key))) {
        if ($field === null) {
            $_POST['successes'][] = 'Mise à jour de la clé "'.$key.'".';
        } else {
            $_POST['successes'][] = 'Mise à jour du champ "'.$field.'".';
        }

        return true;
    } else {
        if ($field === null) {
            $_POST['errors'][] = 'Erreur lors de la mise à jour de la clé "'.$key.'".';
        } else {
            $_POST['errors'][] = 'Erreur lors de la mise à jour du champ "'.$field.'".';
        }

        return false;
    }
}
