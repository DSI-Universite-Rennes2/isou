<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Minishlink\WebPush\VAPID;

require_once PRIVATE_PATH.'/libs/cron.php';

$TITLE .= ' - Configuration des flux de suivi';

$options_yes_no = array(
    '1' => 'Oui',
    '0' => 'Non',
);

if (isset($_POST['rss_enabled'], $_POST['ical_enabled'], $_POST['notifications_enabled'], $_POST['json_enabled']) === true) {
    $post_updates = array();
    $post_updates['ical_enabled'] = array('enabled' => 'UniversiteRennes2\Isou\Event::regenerate_ics', 'disabled' => PUBLIC_PATH.'/isou.ics');
    $post_updates['json_enabled'] = array('enabled' => 'cron_regenerate_json', 'disabled' => PUBLIC_PATH.'/isou.json');

    // Enregistre l'activation des flux.
    foreach (array('rss_enabled', 'ical_enabled', 'json_enabled') as $key) {
        if (isset($options_yes_no[$_POST[$key]]) === false) {
            continue;
        }

        if ($CFG[$key] === $_POST[$key]) {
            continue;
        }

        if (set_configuration($key, $_POST[$key]) === false) {
            continue;
        }

        $CFG[$key] = $_POST[$key];

        // Traitement post-update.
        if (isset($post_updates[$key]) === false) {
            continue;
        }

        if (empty($CFG[$key]) === false) {
            // L'option a été activée.
            call_user_func($post_updates[$key]['enabled']);
            continue;
        }

        // L'option a été désactivée.
        $filename = $post_updates[$key]['disabled'];
        if (is_file($filename) === false) {
            continue;
        }

        if (unlink($filename) === true) {
            $_POST['successes'][] = 'Information : le fichier "'.$filename.'" a été supprimé.';
        } else {
            $_POST['errors'][] = 'Le fichier "'.$filename.'" n\'a pas pu être supprimé.';
        }
    }

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

$SUBTEMPLATE = 'settings/feeds.tpl';
