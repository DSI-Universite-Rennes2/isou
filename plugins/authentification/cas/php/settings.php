<?php

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/authentification/cas/html');

// Hack: instantiate phpCAS to initialize constants.
phpCAS::getVersion();

$options_cas_protocols = array(
    CAS_VERSION_1_0 => 'CAS 1.0',
    CAS_VERSION_2_0 => 'CAS 2.0',
    CAS_VERSION_3_0 => 'CAS 3.0',
    SAML_VERSION_1_1 => 'SAML 1.1',
);

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

$plugin->settings->cas_verbose = strval(intval($plugin->settings->cas_verbose));

if (isset($_POST['plugin_cas_enable'], $options_yes_no[$_POST['plugin_cas_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_cas_enable']) {
        // Vérifie qu'il reste au moins une méthode d'authentification activée.
        if ($_POST['plugin_cas_enable'] === '0') {
            $count_authentification_method = 0;
            foreach ($modules as $module) {
                if ($module->active === '1' && $module->codename !== $plugin->codename) {
                    $count_authentification_method++;
                }
            }

            if ($count_authentification_method === 0) {
                $_POST['errors'][] = 'Il n\'est pas possible de désactiver cette méthode d\'authentification car c\'est la dernière méthode d\'authentification activée.';
            }
        }

        if (isset($_POST['errors'][0]) === false) {
            $plugin->active = $_POST['plugin_cas_enable'];
            $plugin->save();

            if ($plugin->active === '1') {
                $_POST['successes'][] = 'Authentification CAS activée.';
            } else {
                $_POST['successes'][] = 'Authentification CAS désactivée.';
            }
        }
    }
}

// Teste les entrées de type hosts.
foreach (array('host') as $attribute) {
    if (isset($_POST['plugin_cas_'.$attribute]) === true) {
        $post_value = $_POST['plugin_cas_'.$attribute];
        $setting_attribute = 'cas_'.$attribute;

        if ($plugin->settings->$setting_attribute !== $post_value) {
            if (empty($_POST['plugin_cas_'.$attribute]) === true || filter_var($_POST['plugin_cas_'.$attribute], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false) {
                $plugin->settings->$setting_attribute = $post_value;
                $plugin->update_settings($overwrite = true);

                $_POST['successes'][] = 'Champ "'.$attribute.'" enregistré.';
            } else {
                $_POST['errors'][] = 'Le champ "'.$attribute.'" ne contient pas un domaine valide.';
            }
        }
    }
}

// Teste les entrées de type entiers.
foreach (array('port') as $attribute) {
    if (isset($_POST['plugin_cas_'.$attribute]) === true) {
        $post_value = $_POST['plugin_cas_'.$attribute];
        $setting_attribute = 'cas_'.$attribute;

        if ((string) $plugin->settings->$setting_attribute !== $post_value) {
            if (empty($_POST['plugin_cas_'.$attribute]) === true || ctype_digit($_POST['plugin_cas_'.$attribute]) === true) {
                $plugin->settings->$setting_attribute = $post_value;
                $plugin->update_settings($overwrite = true);

                $_POST['successes'][] = 'Champ "'.$attribute.'" enregistré.';
            } else {
                $_POST['errors'][] = 'Le champ "'.$attribute.'" ne contient pas un entier.';
            }
        }
    }
}

// Teste les entrées de type chaines.
foreach (array('path', 'ldap_username', 'ldap_dn', 'ldap_attribute_firstname', 'ldap_attribute_lastname', 'ldap_attribute_email') as $attribute) {
    if (isset($_POST['plugin_cas_'.$attribute]) === true) {
        $post_value = $_POST['plugin_cas_'.$attribute];
        $setting_attribute = 'cas_'.$attribute;

        if ($plugin->settings->$setting_attribute !== $post_value) {
            $plugin->settings->$setting_attribute = $post_value;
            $plugin->update_settings($overwrite = true);

            $_POST['successes'][] = 'Champ "'.$attribute.'" enregistré.';
        }
    }
}

// Teste les entrées de type filtre LDAP.
if (isset($_POST['plugin_cas_ldap_filter']) === true) {
    if ($plugin->settings->cas_ldap_filter !== $_POST['plugin_cas_ldap_filter']) {
        if (empty($_POST['plugin_cas_ldap_filter']) === false && strpos($_POST['plugin_cas_ldap_filter'], ':phpcas_username') === false) {
            $_POST['errors'][] = 'Le champ "'.$attribute.'" ne contient pas la chaine <code>:phpcas_username</code>.';
        } else {
            $plugin->settings->cas_ldap_filter = $_POST['plugin_cas_ldap_filter'];
            $plugin->update_settings($overwrite = true);

            $_POST['successes'][] = 'Champ "ldap_filter" enregistré.';
        }
    }
}

// Teste les entrées de type mots de passe.
if (isset($_POST['plugin_cas_ldap_password']) === true) {
    $post_value = $_POST['plugin_cas_ldap_password'];

    if ($plugin->settings->cas_ldap_password !== $post_value && $post_value !== '* * * * *') {
        $plugin->settings->cas_ldap_password = $post_value;
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Champ "ldap_password" enregistré.';
    }
}

// Teste les entrées de type uri.
foreach (array('ldap_uri', 'logout_redirection') as $attribute) {
    if (isset($_POST['plugin_cas_'.$attribute]) === true) {
        $post_value = $_POST['plugin_cas_'.$attribute];
        $setting_attribute = 'cas_'.$attribute;

        if ($plugin->settings->$setting_attribute !== $post_value) {
            if (empty($post_value) === true || filter_var($post_value, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED) !== false) {
                $plugin->settings->$setting_attribute = $post_value;
                $plugin->update_settings($overwrite = true);

                $_POST['successes'][] = 'Champ "'.$attribute.'" enregistré.';
            } else {
                $_POST['errors'][] = 'Le champ "'.$attribute.'" ne contient pas une url valide avec au moins le protocole et un domaine.';
            }
        }
    }
}

// Teste les entrées de type chemin de fichier.
if (isset($_POST['plugin_cas_certificate_path']) === true) {
    if (empty($_POST['plugin_cas_certificate_path']) === false && is_readable($_POST['plugin_cas_certificate_path']) === false) {
        $_POST['errors'][] = 'Le champ "certificate_path" ne contient pas un fichier lisible.';
    } else {
        $plugin->settings->cas_certificate_path = $_POST['plugin_cas_certificate_path'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Champ "certificate_path" enregistré.';
    }
}

if (isset($_POST['plugin_cas_protocol'], $options_cas_protocols[$_POST['plugin_cas_protocol']]) === true) {
    if ($plugin->settings->cas_protocol !== $_POST['plugin_cas_protocol']) {
        $plugin->settings->cas_protocol = $_POST['plugin_cas_protocol'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Champ "protocol" enregistré.';
    }
}

if (isset($_POST['plugin_cas_verbose'], $options_yes_no[$_POST['plugin_cas_verbose']]) === true) {
    if ($plugin->settings->cas_verbose !== $_POST['plugin_cas_verbose']) {
        $plugin->settings->cas_verbose = $_POST['plugin_cas_verbose'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Champ "verbose" enregistré.';
    }
}

$smarty->assign('options_cas_protocols', $options_cas_protocols);
$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$AUTHENTIFICATION_TEMPLATE = 'settings.tpl';
