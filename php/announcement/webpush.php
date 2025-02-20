<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Notification;
use UniversiteRennes2\Isou\Subscription;

$subscriptions = Subscription::get_records();
$count_subscriptions = count($subscriptions);

if (isset($_POST['message']) === true && $count_subscriptions > 0) {
    if (empty($_POST['message']) === true) {
        $_POST['errors'] = array('Le message ne peut pas être vide.');
    } else {
        $title = $CFG['site_name'];
        $url = $CFG['site_url'];
        $icon = $CFG['site_url'].'/themes/'.$CFG['theme'].'/favicon.png';
        $message = $_POST['message'];

        $notification = new Notification($title, $message, $url, $icon);
        $webpush = $notification->get_webpush();

        $LOGGER->info('Envoi de '.$count_subscriptions.' notification(s) web par '.$USER->username.' (#'.$USER->id.')');
        $LOGGER->info('Message : '.$message);

        $count_successes = 0;
        $count_errors = 0;
        foreach ($subscriptions as $subscription) {
            $response = $subscription->notify($webpush, $notification);

            if ($response->isSubscriptionExpired() === true) {
                $LOGGER->info('Souscription #'.$subscription->id.' expirée pour l\'utilisateur #'.$subscription->iduser);
                $subscription->delete();
                $count_errors++;
            } elseif ($response->isSuccess() === false) {
                $LOGGER->info('Envoi de la souscription #'.$subscription->id.' pour l\'utilisateur #'.$subscription->iduser.' a échoué ('.$response->getReason().')');
                $count_errors++;
            } else {
                $LOGGER->debug('Souscription #'.$subscription->id.' envoyée à l\'utilisateur #'.$subscription->iduser);
                $count_successes++;
            }
        }

        if ($count_successes === 0) {
            $result = 'Aucune notification envoyée.';
        } elseif ($count_successes === 1) {
            $result = '1 notification envoyée.';
        } else {
            $result = $count_successes.' notifications envoyées.';
        }

        if ($count_errors === 1) {
            $result .= ' (1 erreur)';
        } elseif ($count_errors > 1) {
            $result .= ' ('.$count_errors.' erreurs)';
        }

        $_SESSION['messages'] = array('successes' => $result);

        header('Location: '.URL.'/index.php/annonce/notification');
        exit(0);
    }
}

$smarty->assign('count_subscriptions', $count_subscriptions);

$subtemplate = 'announcement/webpush.tpl';
