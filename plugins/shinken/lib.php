<?php

use UniversiteRennes2\Isou\State;

function plugin_shinken_update($plugin = null) {
    global $CFG, $LOGGER;

    // TODO: load CFG.
    $url = $CFG['plugins']['shinken']['url'];
    $username = $CFG['plugins']['shinken']['username'];
    $password = $CFG['plugins']['shinken']['password'];

    // Appel le webservice de Shinken.
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
        $LOGGER->addError('Les données de l\'API Shinken n\'ont pas pu être lues.');

        return false;
    }

    $elements = json_decode($response);
    if ($elements === null) {
        $LOGGER->addError('Le JSON reçu par l\'API Shinken n\'a pas pu être décodé.');

        return false;
    }

    $services = array();
    foreach ($elements as $element) {
        if (isset($element->description) === true) {
            $service = new stdClass();
            $service->name = $element->description.'@'.$element->host_name;
            $service->state = $element->state;

            if (empty($element->is_flapping) === false) {
                $service->state = State::WARNING;
            }

            $id = md5($service->name);
            if (isset($services[$id]) === false) {
                $services[$id] = $service;
            } else {
                $LOGGER->addInfo('Un service Shinken porte déjà le nom "'.$service->name.'" (id: '.$id.').');
            }
        }
    }

    // Enregistre le cache.
    $cache_path = PRIVATE_PATH.'/cache/plugins/shinken';

    if (is_dir($cache_path) === false) {
        if (mkdir($cache_path, 0755, $recursive = true) === false) {
            $_SESSION['messages']['errors'][] = 'Impossible de créer le dossier "'.$cache_path.'"';
        }
    }

    if (file_put_contents($cache_path.'/services.json', json_encode($services, JSON_PRETTY_PRINT)) === false) {
        $LOGGER->addError('Le cache n\'a pas pu être écrit dans le répertoire "'.$cache_path.'".');
    }

    // Mets à jour les états des services Shinken dans la base de données d'Isou.
    foreach (get_services(array('plugin' => PLUGIN_SHINKEN)) as $service) {
        $id = md5($service->name);

        if (isset($services[$id]) === false) {
            continue;
        }

        if ($service->state !== $services[$id]->state) {
            $LOGGER->addInfo('   Le service "'.$service->name.'" (id #'.$service->id.') passe de l\'état '.$service->state.' à '.$services[$id]->state.'.');
            $service->state = $services[$id]->state;
            $service->save();
        }
    }

    return true;
}
