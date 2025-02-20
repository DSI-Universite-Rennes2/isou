<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\User;

/**
 * Authentifie l'utilisateur ou retourne le template d'authentification.
 *
 * @param Plugin $plugin Instance du plugin manual.
 *
 * @return string|void
 */
function authentication_login(Plugin $plugin) {
    global $smarty;

    if (isset($_POST['username'], $_POST['password']) === true) {
        $user = User::get_record(array('username' => $_POST['username'], 'authentication' => 'manual'));

        if ($user !== false) {
            if (password_verify($_POST['password'], $user->password) === true) {
                $_SESSION['username'] = $user->username;
                $_SESSION['authentication'] = 'manual';

                $user->lastaccess = new \DateTime();
                $user->save();

                header('Location: '.URL);
                exit(0);
            }
        }

        $_POST['errors'][] = 'Les informations transmises n\'ont pas permis de vous authentifier.';
    }

    $smarty->addTemplateDir(PRIVATE_PATH.'/plugins/authentication/manual/html');

    return $smarty->fetch('login.tpl');
}

/**
 * Déconnecte l'utilisateur.
 *
 * @param Plugin|null $plugin Instance du plugin manual.
 *
 * @return void
 */
function authentication_logout(?Plugin $plugin = null) {
    $_SESSION = array();

    if (isset($_COOKIE[session_name()]) === true) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();

    header('Location: '.URL);
    exit(0);
}
