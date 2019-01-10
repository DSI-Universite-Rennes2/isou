<?php

if (defined('VAPID_PUBLIC_KEY') === true && is_readable(VAPID_PUBLIC_KEY) === true) {
    $key = file_get_contents(VAPID_PUBLIC_KEY);
}

$response = new stdClass();
if ($key === false) {
    $response->status = 500;
    $response->message = 'La clé publique du serveur n\'est pas disponible.';
} else {
    $response->status = 200;
    $response->message = $key;
}

http_response_code($response->status);

echo json_encode($response);
exit(0);
