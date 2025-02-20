<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

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

/**
 * Retourne la configuration d'isou.
 *
 * @return array
 */
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
        while (($config = $query->fetch(PDO::FETCH_OBJ)) !== false) {
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

/**
 * Retourne la liste des plugins de monitoring.
 *
 * @return array
 */
function get_plugins() {
    $plugins = Plugin::get_records(array('type' => 'monitoring'));
    foreach ($plugins as $plugin) {
        define('PLUGIN_'.strtoupper($plugin->codename), $plugin->id);
    }

    return $plugins;
}

/**
 * Enregistre un paramètre de la configuration générale.
 *
 * @param string $key Nom du paramètre.
 * @param string $value Valeur du paramètre.
 * @param string|null $field Libellé du paramètre.
 *
 * @return boolean
 */
function set_configuration(string $key, string $value, ?string $field = null) {
    global $DB;

    $sql = "UPDATE configuration SET value=? WHERE key=?";
    $query = $DB->prepare($sql);
    if ($query->execute(array($value, $key)) === true) {
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
