<?php

/**
 * Fonctions liées aux mises à jour des services du plugin Nagios.
 */

use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

/**
 * Mets l'état des services du plugin Nagios.
 *
 * @param UniversiteRennes2\Isou\Plugin $plugin Une instance du plugin Nagios.
 *
 * @return boolean True si la mise à jour s'est déroulée correctement ; False si une erreur est survenue.
 */
function plugin_nagios_update($plugin) {
    global $LOGGER;

    // Vérifie si le fichier est lisible.
    if (is_readable($plugin->settings->statusdat_path) === false) {
        $LOGGER->addError('Le fichier '.$plugin->settings->statusdat_path.' ne peut pas être lu.');

        return false;
    }

    // Vérifie si le fichier peut être ouvert.
    $handle = @fopen($plugin->settings->statusdat_path, 'r');
    if ($handle === false) {
        $LOGGER->addError('Le fichier '.$plugin->settings->statusdat_path.' n\'a pu être ouvert.');

        return false;
    }

    // Parse le fichier status.dat de Nagios.
    $services = array();
    while (feof($handle) === false) {
        $line = trim(fgets($handle, 4096));
        if (preg_match('/^servicestatus \{/', $line) === 1) {
            $service = new stdClass();
            $service->name = '@';
            $service->state = 0;

            while (feof($handle) === false) {
                $line = trim(fgets($handle, 4096));
                if ($line === '}') {
                    // Fin de la description du service.
                    break;
                }

                if (preg_match('/host_name=|service_description=|current_state=|is_flapping=/', $line) === 1) {
                    list($key, $value) = explode('=', $line, 2);

                    switch ($key) {
                        case 'host_name':
                            $service->name .= $value;
                            break;
                        case 'service_description':
                            $service->name = $value.$service->name;
                            break;
                        case 'current_state':
                            $service->state = $value;
                            break;
                        case 'is_flapping':
                            if (empty($value) === false) {
                                $service->state = State::WARNING;
                            }
                    }
                }
            }

            $id = md5($service->name);
            if (isset($services[$id]) === false) {
                $services[$id] = $service;
            } else {
                $LOGGER->addInfo('Un service Nagios porte déjà le nom "'.$service->name.'" (id: '.$id.').');
            }
        }
    }
    fclose($handle);

    // Enregistre le cache.
    $cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/nagios';

    if (is_dir($cache_path) === false) {
        if (mkdir($cache_path, 0755, $recursive = true) === false) {
            $LOGGER->addError('Impossible de créer le dossier "'.$cache_path.'"');
        }
    }

    if (file_put_contents($cache_path.'/services.json', json_encode($services, JSON_PRETTY_PRINT)) === false) {
        $LOGGER->addError('Le cache n\'a pas pu être écrit dans le répertoire "'.$cache_path.'".');
    }

    // Mets à jour les états des services Nagios dans la base de données d'Isou.
    foreach (Service::get_records(array('plugin' => PLUGIN_NAGIOS)) as $service) {
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
