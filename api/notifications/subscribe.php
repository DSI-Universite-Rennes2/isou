<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

use UniversiteRennes2\Isou\Subscription;

if (isset($_POST['endpoint'], $_POST['publicKey'], $_POST['authToken'], $_POST['contentEncoding']) === true) {
    $response = new stdClass();
    $response->status = 200;
    $response->message = '';

    $subscription = Subscription::get_record(array('endpoint' => $_POST['endpoint'], 'public_key' => $_POST['publicKey'], 'authentification_token' => $_POST['authToken']));
    if ($subscription === false) {
        $subscription = new Subscription();
        $subscription->endpoint = $_POST['endpoint'];
        $subscription->public_key = $_POST['publicKey'];
        $subscription->authentification_token = $_POST['authToken'];
        $subscription->content_encoding = $_POST['contentEncoding'];
        $subscription->iduser = $USER->id;

        try {
            $subscription->save();

            $response->status = 200;
            $response->message = 'Inscription enregistrée.';
        } catch (Exception $exception) {
            $LOGGER->addError($exception->getMessage());

            $response->status = 500;
            $response->message = 'Une erreur est survenue lors de l\'inscription.';
        }
    } else {
        $response->status = 204;
        $response->message = 'Inscription déjà enregistrée.';
    }

    http_response_code($response->status);

    echo json_encode($response);
    exit(0);
}
