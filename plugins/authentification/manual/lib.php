<?php

use UniversiteRennes2\Isou\User;

/**
  * Authentifie l'utilisateur ou retourne le template d'authentification.
  *
  * @param Plugin $plugin Instance du plugin manual.
  *
  * @return string|void
  */
function authentification_login($plugin) {
    global $smarty;

    if (isset($_POST['username'], $_POST['password']) === true) {
        $user = User::get_record(array('username' => $_POST['username'], 'authentification' => 'manual'));

        if ($user !== false) {
            if (password_verify($_POST['password'], $user->password) === true) {
                $_SESSION['username'] = $user->username;
                $_SESSION['authentification'] = 'manual';

                $user->lastaccess = new \DateTime();
                $user->save();

                header('Location: '.URL);
                exit(0);
            }
        }

        $_POST['errors'][] = 'Les informations transmises n\'ont pas permis de vous authentifier.';
    }

    $smarty->addTemplateDir(PRIVATE_PATH.'/plugins/authentification/manual/html');

    return $smarty->fetch('login.tpl');
}

/**
  * Déconnecte l'utilisateur.
  *
  * @return void
  */
function authentification_logout() {
    $_SESSION = array();

    if (isset($_COOKIE[session_name()]) === true) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();

    header('Location: '.URL);
    exit(0);
}
