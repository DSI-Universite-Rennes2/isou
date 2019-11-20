<?php

use UniversiteRennes2\Isou\User;

session_name('isou');
session_start();

if (is_file(__DIR__.'/../config.php') === false) {
    echo 'L\'application ne semble pas être installée.'.
        ' Veuillez suivre la <a href="https://github.com/DSI-Universite-Rennes2/isou#installation-et-mise-%C3%A0-jour" target="_blank">procédure d\'installation</a>.';
    exit(1);
}

require __DIR__.'/../config.php';
require PRIVATE_PATH.'/php/common/database.php';

require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

$USER = false;
if (isset($_SESSION['username'], $_SESSION['authentification']) === true) {
    $USER = User::get_record(array('username' => $_SESSION['username'], 'authentification' => $_SESSION['authentification']));
}

$PAGE_NAME = explode('/', get_page_name(basename(__FILE__)));

switch ($PAGE_NAME[0]) {
    case 'notifications':
        require PRIVATE_PATH.'/api/notifications/index.php';
        break;
}

// Retourne par défaut une réponse 404.
http_response_code(404);
