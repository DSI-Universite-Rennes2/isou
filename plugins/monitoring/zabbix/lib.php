<?php
/**
 * This file is part of isou project.
 *
 * Fonctions liées aux mises à jour des services du plugin Zabbix.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

/**
 * Met l'état des services du plugin Zabbix.
 *
 * @param Plugin $plugin Une instance du plugin Zabbix.
 *
 * @return boolean True si la mise à jour s'est déroulée correctement ; False si une erreur est survenue.
 */
function plugin_zabbix_update(Plugin $plugin) {
    global $LOGGER;

    $url = $plugin->settings->zabbix_url;
    if (empty($url) === true) {
        $LOGGER->error('Le paramètre "URL de Zabbix" semble être vide.');

        return false;
    }

    $apitoken = $plugin->settings->zabbix_api_token;
    $equals = 1;

    $tags = array();
    if (empty($plugin->settings->zabbix_tags) === false) {
        foreach (explode(',', $plugin->settings->zabbix_tags) as $tagvalue) {
            list($tag, $value) = explode('=', $tagvalue);
            $tags[] = array('tag' => $tag, 'value' => $value, 'operator' => $equals);
        }
    }

    // Appelle le webservice de Zabbix.
    $params = array('jsonrpc' => '2.0', 'method' => 'host.get', 'params' => array('selectItems' => array('key_', 'status'), 'output' => array('host'), 'inheritedTags' => true), 'id' => rand());
    if (isset($tags[0]) === true) {
        $params['params']['tags'] = $tags;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json-rpc', 'Authorization: Bearer '.$apitoken));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    $data = curl_exec($curl);
    $error = curl_errno($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Vérifie si le fichier peut être ouvert.
    if ($error !== 0) {
        $LOGGER->error('L\'url "'.$url.'" n\'est pas accessible.');

        return false;
    }

    if ($http_code > 399) {
        $LOGGER->error('Le code de retour HTTP n\'est pas correct (code '.$http_code.')');

        return false;
    }

    $response = json_decode($data);
    if ($response === false) {
        $LOGGER->error('Les données de l\'API Zabbix n\'ont pas pu être lues.');

        return false;
    }

    if (isset($response->result) === false) {
        $LOGGER->error('Le JSON reçu par l\'API Zabbix n\'a pas pu être décodé.');

        return false;
    }

    $services = array();
    foreach ($response->result as $element) {
        if (isset($element->host) === false) {
            continue;
        }

        if (isset($element->items) === false) {
            continue;
        }

        foreach ($element->items as $item) {
            if (isset($item->key_) === false) {
                continue;
            }

            if (isset($item->status) === false) {
                continue;
            }

            $service = new stdClass();
            $service->name = $item->key_.'@'.$element->host;
            $service->state = (string) $item->status;

            $id = md5($service->name);
            if (isset($services[$id]) === false) {
                $services[$id] = $service;
            } else {
                $LOGGER->info('Un service Zabbix porte déjà le nom "'.$service->name.'" (id: '.$id.').');
            }
        }
    }

    // Enregistre le cache.
    $cache_path = PRIVATE_PATH.'/cache/plugins/monitoring/zabbix';

    if (is_dir($cache_path) === false) {
        if (mkdir($cache_path, 0755, $recursive = true) === false) {
            $LOGGER->error('Impossible de créer le dossier "'.$cache_path.'"');
        }
    }

    if (file_put_contents($cache_path.'/services.json', json_encode($services, JSON_PRETTY_PRINT)) === false) {
        $LOGGER->error('Le cache n\'a pas pu être écrit dans le répertoire "'.$cache_path.'".');
    }

    // Met à jour les états des services Zabbix dans la base de données d'Isou.
    foreach (Service::get_records(array('plugin' => PLUGIN_ZABBIX)) as $service) {
        $id = md5($service->name);

        if (isset($services[$id]) === false) {
            continue;
        }

        if ($service->state !== $services[$id]->state) {
            $LOGGER->info('   Le service "'.$service->name.'" (id #'.$service->id.') passe de l\'état '.$service->state.' à '.$services[$id]->state.'.');
            $service->change_state($services[$id]->state);
        }
    }

    return true;
}
