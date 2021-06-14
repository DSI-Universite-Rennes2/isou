<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

/**
 * Retourne les arguments passés à la requête HTTP.
 *
 * Exemple :
 *    -> URL appelée : https://services.univ-rennes2.fr/isou/index.php/configuration/informations
 *    <- valeur retournée : configuration/informations
 *
 * @param string $script_called Nom du script PHP appelé.
 *
 * @return string
 */
function get_page_name(string $script_called = 'index.php') {
    $uri = $_SERVER["REQUEST_URI"];

    $pos = strpos($uri, '/'.$script_called);

    $var = strpos($uri, '?');
    if ($var !== false) {
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
 * @param boolean $force_https Si True, force l'utilisation du HTTPS.
 *
 * @return string
 */
function get_base_url(bool $force_https = false) {
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

    if (isset($_SERVER['SERVER_PORT']) === true && in_array($_SERVER['SERVER_PORT'], array('80', '443'), $strict = true) === false) {
        $_SERVER['SERVER_NAME'] .= ':'.$_SERVER['SERVER_PORT'];
    }

    return rtrim($scheme.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']), '/');
}
