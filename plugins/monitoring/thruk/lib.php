<?php
/**
 * This file is part of isou project.
 *
 * Fonctions liées aux mises à jour des services du plugin Thruk.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

/**
 * Met l'état des services du plugin Thruk.
 *
 * @param Plugin $plugin Une instance du plugin Thruk.
 *
 * @return boolean True si la mise à jour s'est déroulée correctement ; False si une erreur est survenue.
 */
function plugin_thruk_update(Plugin $plugin) {
    global $LOGGER;

    $url = $plugin->settings->thruk_path;
    if (empty($url) === true) {
        $LOGGER->addError('Le paramètre "URL de Thruk" semble être vide.');

        return false;
    }

    $username = $plugin->settings->thruk_username;
    $password = $plugin->settings->thruk_password;

    // Appelle le webservice de Thruk.
    $url .= '?host=all&view_mode=json&columns=host_name,description,state,acknowledged,is_flapping';

    $params = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Basic '.base64_encode($username.':'.$password),
        ),
    );

    // Vérifie si le fichier peut être ouvert.
    $handle = @fopen($url, $mode = 'rb', $use_include_path = false, stream_context_create($params));
    if ($handle === false) {
        $LOGGER->addError('L\'url "'.$url.'" n\'est pas accessible.');

        return false;
    }

    $response = @stream_get_contents($handle);
    fclose($handle);

    if ($response === false) {
        $LOGGER->addError('Les données de l\'API Thruk n\'ont pas pu être lues.');

        return false;
    }

    $elements = json_decode($response);
    if ($elements === null) {
        $LOGGER->addError('Le JSON reçu par l\'API Thruk n\'a pas pu être décodé.');

        return false;
    }

    $services = array();
    foreach ($elements as $element) {
        if (isset($element->description) === true) {
            $service = new stdClass();
            $service->name = $element->description.'@'.$element->host_name;
            $service->state = (string) $element->state;

            if (empty($element->is_flapping) === false) {
                $service->state = State::WARNING;
            }

            if (empty($element->acknowledged) === false) {
                $service->state = State::OK;
            }

            $id = md5($service->name);
            if (isset($services[$id]) === false) {
                $services[$id] = $service;
            } else {
                $LOGGER->addInfo('Un service Thruk porte déjà le nom "'.$service->name.'" (id: '.$id.').');
            }
        }
    }

    // Enregistre le cache.
    $cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/thruk';

    if (is_dir($cache_path) === false) {
        if (mkdir($cache_path, 0755, $recursive = true) === false) {
            $LOGGER->addError('Impossible de créer le dossier "'.$cache_path.'"');
        }
    }

    if (file_put_contents($cache_path.'/services.json', json_encode($services, JSON_PRETTY_PRINT)) === false) {
        $LOGGER->addError('Le cache n\'a pas pu être écrit dans le répertoire "'.$cache_path.'".');
    }

    // Met à jour les états des services Thruk dans la base de données d'Isou.
    foreach (Service::get_records(array('plugin' => PLUGIN_THRUK)) as $service) {
        $id = md5($service->name);

        if (isset($services[$id]) === false) {
            continue;
        }

        if ($service->state !== $services[$id]->state) {
            $LOGGER->addInfo('   Le service "'.$service->name.'" (id #'.$service->id.') passe de l\'état '.$service->state.' à '.$services[$id]->state.'.');
            $service->change_state($services[$id]->state);
        }
    }

    return true;
}
