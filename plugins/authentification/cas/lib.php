<?php

/**
  * Authentifie l'utilisateur ou redirige vers le formulaire d'authentification CAS.
  *
  * @param Plugin $plugin Instance du plugin CAS.
  *
  * @return void
  */
function authentification_login($plugin) {
    phpCAS::setVerbose($plugin->settings->cas_verbose);

    // Initialize phpCAS client.
    phpCAS::client($plugin->settings->cas_protocol, $plugin->settings->cas_host, $plugin->settings->cas_port, $plugin->settings->cas_path);

    if (isset($_SESSION['username']) === false) {
        phpCAS::forceAuthentication();
    }

    $_SESSION['username'] = phpCAS::getUser();
    $_SESSION['authentification'] = 'cas';
    $USER = User::get_record(array('username' => $_SESSION['username'], 'authentification' => 'cas'));
    if ($USER === false) {
        $USER = new User();
        $USER->authentification = 'cas';
        $USER->username = $_SESSION['username'];
        $USER->admin = '0';

        try {
            $USER->save();
        } catch (\Exception $exception) {
            unset($_SESSION['username'], $_SESSION['authentification']);
            $_POST['errors'][] = $exception->getMessage();
        }
    }

    $settings = (empty($plugin->settings->cas_ldap_uri) === false && empty($plugin->settings->cas_ldap_dn) === false && empty($plugin->settings->cas_ldap_filter) === false);
    if (isset($_SESSION['username']) === true && $settings === true) {
        $ldap_connect = ldap_connect($plugin->settings->cas_ldap_uri);
        if ($ldap !== false) {
            if (empty($plugin->settings->cas_ldap_username) === false) {
                if (empty($plugin->settings->cas_ldap_password) === true) {
                    $plugin->settings->cas_ldap_password = null;
                }
                $ldap_bind = ldap_bind($ldap_connect, $plugin->settings->cas_ldap_username, $plugin->settings->cas_ldap_password);
            } else {
                $ldap_bind = ldap_bind($ldap_connect);
            }

            if ($ldap_bind === true) {
                $attributes = array();
                foreach (array('firstname', 'lastname', 'email') as $name) {
                    $attribute = 'cas_ldap_attribute_'.$name;
                    if (empty($plugin->settings->$attribute) === false) {
                        $attributes[$name] = $plugin->settings->$attribute;
                    }
                }

                $ldap_filter = str_replace(':phpcas_username', $_SESSION['username'], $plugin->settings->cas_ldap_filter);
                $ldap_search = ldap_search($ldap_connect, $plugin->settings->cas_ldap_dn, $ldap_filter, array_values($attributes));

                if ($ldap_search !== false) {
                    $entries = ldap_get_entries($ldap_connect, $ldap_search);
                    if ($entries['count'] === 1) {
                        foreach (array('firstname', 'lastname', 'email') as $name) {
                            if (isset($attributes[$name], $entries[0][$attributes[$name]][0]) === true) {
                                $USER->{$name} = $entries[0][$attributes[$name]][0];
                            }
                        }

                        $USER->admin = '1';
                    } else {
                        // L'utilisateur n'a plus le droit d'accéder à l'administration.
                        $USER->admin = '0';
                    }

                    try {
                        $USER->save();
                    } catch (\Exception $exception) {
                        unset($_SESSION['username'], $_SESSION['authentification']);
                        $_POST['errors'][] = $exception->getMessage();
                    }
                }
            }
        }
    }
}

/**
  * Déconnecte l'utilisateur.
  *
  * @param Plugin $plugin Instance du plugin CAS.
  *
  * @return void
  */
function authentification_logout($plugin) {
    $_SESSION = array();

    if (isset($_COOKIE[session_name()]) === true) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();

    if (empty($plugin->settings->cas_logout_redirection) === true) {
        phpCAS::logout();
        exit(0);
    }

    phpCAS::logoutWithRedirectService($plugin->settings->cas_logout_redirection);
    exit(0);
}
