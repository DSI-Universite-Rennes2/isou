<?php

/**
  * Retourne les arguments passés à la requête HTTP.
  *
  * Exemple :
  *    -> URL appelée : https://services.univ-rennes2.fr/isou/index.php/configuration/informations
  *    <- valeur retournée : configuration/informations
  *
  * @var    string $script_called
  * @return string
  */
function get_page_name($script_called = 'index.php') {
    $uri = $_SERVER["REQUEST_URI"];

    $pos = strpos($uri, '/'.$script_called);

    if ($var = strpos($uri, '?')) {
        $uri = substr($uri, 0, $var);
    }

    return substr($uri, (strlen(' /'.$script_called) + $pos));
}

/**
  * Retourne la base de l'URL appelée sans le nom du script appelé.
  *
  * Exemple :
  *    -> URL appelée : https://services.univ-rennes2.fr/isou/index.php/configuration/informations
  *    <- valeur retournée : https://services.univ-rennes2.fr/isou
  *
  * @var    boolean $force_https Si True, force l'utilisation du HTTPS.
  * @return string
  */
function get_base_url($force_https = false) {
    if ($force_https === true || (isset($_SERVER['HTTPS']) === true && empty($_SERVER['HTTPS']) === false)) {
        $scheme = 'https://';
    } else {
        $scheme = 'http://';
    }

    if (isset($_SERVER['SERVER_NAME']) === false) {
        $_SERVER['SERVER_NAME'] = '.';
    }

    if (isset($_SERVER["HTTP_X_FORWARDED_HOST"]) === true) {
        $_SERVER['SERVER_NAME'] = $_SERVER["HTTP_X_FORWARDED_HOST"];
    }

    if (isset($_SERVER['SERVER_PORT']) === true && in_array($_SERVER['SERVER_PORT'], array(80, 443), true) === false) {
        $_SERVER['SERVER_NAME'] .= ':'.$_SERVER['SERVER_PORT'];
    }

    return $scheme.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
}
