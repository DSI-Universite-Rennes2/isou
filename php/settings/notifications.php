<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Minishlink\WebPush\VAPID;

$TITLE .= ' - Configuration des notifications par webpush';

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['notifications_enabled'], $_POST['http_proxy'], $_POST['https_proxy'], $_POST['no_proxy']) === true) {
    // Enregistre l'activation des notifications.
    if (set_configuration('notifications_enabled', $_POST['notifications_enabled']) === true) {
        $CFG['notifications_enabled'] = $_POST['notifications_enabled'];

        if (empty($CFG['notifications_enabled']) === false) {
            foreach (array('VAPID_PRIVATE_KEY', 'VAPID_PUBLIC_KEY') as $key) {
                if (defined($key) === false) {
                    $_POST['errors'][] = 'La constante <code>'.$key.'</code> n\'est pas défini dans le fichier <code>config.php</code>.'.
                        ' Définissez-la, en vous basant sur le fichier <code>distribution/config.php</code>.';
                }
            }

            if (empty($CFG['site_url']) === true) {
                set_configuration('site_url', URL.'/');
            }

            if (isset($_POST['errors'][0]) === false) {
                $keys = VAPID::createVapidKeys();
                if (empty($keys) === false) {
                    if (file_put_contents(VAPID_PRIVATE_KEY, $keys['privateKey']) === false) {
                        $_POST['errors'][] = 'La clé privée VAPID n\'a pas pu être enregistrée. Désactivez, puis réactivez les notifications pour regénérer la clé.';
                    }

                    if (file_put_contents(VAPID_PUBLIC_KEY, $keys['publicKey']) === false) {
                        $_POST['errors'][] = 'La clé publique VAPID n\'a pas pu être enregistrée. Désactivez, puis réactivez les notifications pour regénérer la clé.';
                    }
                }
            }
        }
    }

    // Enregistre les paramètres du proxy.
    if ($CFG['http_proxy'] !== $_POST['http_proxy'] && set_configuration('http_proxy', $_POST['http_proxy']) === true) {
        $CFG['http_proxy'] = $_POST['http_proxy'];
    }

    if ($CFG['https_proxy'] !== $_POST['https_proxy'] && set_configuration('https_proxy', $_POST['https_proxy']) === true) {
        $CFG['https_proxy'] = $_POST['https_proxy'];
    }

    $no_proxy = array();
    foreach (explode(',', $_POST['no_proxy']) as $value) {
        $value = trim($value);

        if (empty($value) === true) {
            continue;
        }

        $no_proxy[] = $value;
    }

    if ($no_proxy === array()) {
        if (empty($no_proxy) !== empty($CFG['no_proxy']) && set_configuration('no_proxy', '') === true) {
            $CFG['no_proxy'] = null;
        }
    } else {
        if ($no_proxy !== $CFG['no_proxy'] && set_configuration('no_proxy', json_encode($no_proxy)) === true) {
            $CFG['no_proxy'] = $no_proxy;
        }
    }
}

if (empty($CFG['no_proxy']) === true) {
    $no_proxy = '';
} else {
    $no_proxy = implode(', ', $CFG['no_proxy']);
}

$smarty->assign('options_yes_no', $options_yes_no);
$smarty->assign('no_proxy', $no_proxy);

$SUBTEMPLATE = 'settings/notifications.tpl';
