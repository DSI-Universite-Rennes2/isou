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

if (isset($_POST['notifications_enabled']) === true) {
    // Enregistre l'activation des notifications.
    if ($CFG['notifications_enabled'] !== $_POST['notifications_enabled'] && set_configuration('notifications_enabled', $_POST['notifications_enabled']) === true) {
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
                $keys = array();
                if (is_file(VAPID_PRIVATE_KEY) === false || is_file(VAPID_PUBLIC_KEY) === false) {
                    // On génère une paire de clés uniquement si la paire n'est pas complète.
                    $keys = VAPID::createVapidKeys();
                }

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
}

$smarty->assign('options_yes_no', $options_yes_no);

$SUBTEMPLATE = 'settings/notifications.tpl';
