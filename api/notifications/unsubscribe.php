<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Subscription;

// Initialise la variable DELETE avec les paramètres de la requête HTTP.
parse_str(file_get_contents('php://input'), $_DELETE);

if (isset($_DELETE['endpoint'], $_DELETE['publicKey'], $_DELETE['authToken'], $_DELETE['contentEncoding']) === true) {
    $response = new stdClass();
    $response->status = 200;
    $response->message = '';

    $subscription = Subscription::get_record(array('endpoint' => $_DELETE['endpoint'], 'public_key' => $_DELETE['publicKey'], 'authentication_token' => $_DELETE['authToken']));
    if ($subscription !== false) {
        try {
            $subscription->delete();

            $response->status = 200;
            $response->message = 'Déinscription enregistrée.';
        } catch (Exception $exception) {
            $LOGGER->error($exception->getMessage());

            $response->status = 500;
            $response->message = 'Une erreur est survenue lors de la désinscription.';
        }
    } else {
        $response->status = 404;
        $response->message = 'Inscription non trouvée.';
    }

    http_response_code($response->status);

    echo json_encode($response);
    exit(0);
}
