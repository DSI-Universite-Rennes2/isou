<?php
/**
 * This file is part of isou project.
 *
 * Fonctions liées aux procédures du crontab.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Dependency_Message;
use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Notification;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;
use UniversiteRennes2\Isou\Subscription;

/**
 * Met à jour Isou en fonction des changements d'état des services backend, des évènements prévues, fermés, etc...
 *
 * @return void
 */
function update_services_tree() {
    global $CFG, $DB, $LOGGER;

    // TODO: utilisé la propriété timemodified.
    $services = Service::get_records();

    $LOGGER->info('Mise à jour de l\'arbre des dépendances');

    // Parcourt tous les services.
    // Pour chaque service, nous mettons à jour les services qui dépendent de ce service.
    while (current($services) !== false) {
        $parent_service = array_shift($services);

        // Définit tous les enfants de ce service et de son état courant.
        $parent_service->set_reverse_dependencies($parent_service->state);

        // $LOGGER->debug('Recherche des dépendances pour le service '.$parent_service->name.' (id #'.$parent_service->id.')');
        // $LOGGER->debug('   '.count($parent_service->reverse_dependencies).' groupes dépendent du service "'.$parent_service->name.'" (avec l\'état: '.$parent_service->state.')');

        // Parcourt chaque enfant.
        foreach ($parent_service->reverse_dependencies as $dependencies_group) {
            $child_service = Service::get_record(array('id' => $dependencies_group->idservice, 'enable' => true));

            // Si l'enfant n'existe plus ou n'est plus actif, on ne fait rien.
            if ($child_service === false) {
                $LOGGER->error('   Le service avec l\'id #'.$dependencies_group->idservice.' n\'existe pas ou plus.'.
                    ' Il est pourtant lié avec le service "'.$parent_service->name.'" (id #'.$parent_service->id.') dans le groupe "'.$dependencies_group->name.'" (id #'.$dependencies_group->id.')');
                continue;
            }

            // Si le service enfant est verrouillé, on ne fait rien.
            if ($child_service->locked === '1') {
                // TODO: vérifier si un évènement existe... sinon, le créer.
                $LOGGER->info('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') est actuellement en mode forcé. Il ne peut pas être mis à jour.');
                continue;
            }

            // On récupère l'évènement en cours de l'enfant.
            $event = $child_service->get_current_event();

            // Si il y a un évènement en cours de type régulier ou de fermeture, on ne fait rien.
            if ($event !== false && in_array($event->type, array(Event::TYPE_REGULAR, Event::TYPE_CLOSED), $strict = true) === true) {
                $LOGGER->info('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') a actuellement une interruption régulière en cours ou est fermé. Il ne peut pas être mis à jour.');
                continue;
            }

            $LOGGER->info('   Analyse du groupe "'.$dependencies_group->name.'" (id #'.$dependencies_group->id.') attaché au service "'.$child_service->name.'" (id #'.$child_service->id.')');

            if ($dependencies_group->redundant === '1') {
                // Si le goupe est redondé... on cherche une dépendance qui fonctionne bien.
                $state = State::UNKNOWN;

                // TODO: faire en sorte de ne pas avoir à recalculer cette boucle à chaque fois.
                $redundant_services = Service::get_records(array('dependencies_group' => $dependencies_group->id));
                foreach ($redundant_services as $redundant_service) {
                    // On cherche au moins un service qui fonctionnerait.
                    if ($state > $redundant_service->state) {
                        $state = $redundant_service->state;
                    }
                }

                $LOGGER->info('      État du groupe : '.State::$STATES[$state]);

                if (in_array($state, array(State::OK, State::CLOSED), $strict = true) === true) {
                    // Un service semble fonctionné dans le groupe redondé. Il n'y a donc pas de problèmes.
                    continue;
                }
            } else {
                // Si le goupe n'est pas redondé... on prend l'état du groupe de dépendances.
                $state = $dependencies_group->groupstate;

                $LOGGER->info('      État du groupe : '.State::$STATES[$state]);
            }

            if ($child_service->state < $state) {
                // Le niveau d'état du groupe est plus important que l'état actuel du service.
                // On change l'état du service et ajoute un évènement au besoin.
                $LOGGER->info('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') passe de l\'état '.$child_service->state.' à '.$dependencies_group->groupstate.'.');
                $event = $child_service->change_state($state);
            } else {
                // Ce groupe de dépendance n'a pas de problèmes. On continue à parcourir les autres groupes de dépendances.
                continue;
            }

            // Si un évènement a été créé précédemment, on met à jour le message de l'évènement si nécessaire.
            $message = Dependency_Message::get_record(array('id' => $dependencies_group->idmessage));

            $description = null;
            if (empty($message->message) === false) {
                if (isset($event->description->description) === true) {
                    $description = $event->description->description;
                } else {
                    $description = $event->description;
                }

                if (empty($description) === true) {
                    $description = $message->message;
                } elseif (stripos($description, $message->message) === false) {
                    $description .= "\n".$message->message;
                } else {
                    $description = null;
                }
            }

            if ($description !== null) {
                try {
                    $DB->beginTransaction();

                    $event->set_description($description, $autogen = true);
                    $event->save();

                    $DB->commit();
                } catch (Exception $exception) {
                    $DB->rollBack();
                    $LOGGER->error('Erreur lors de l\'enregistrement de l\'évènement: '.$exception->getMessage());
                }
            }

            // Vérifions que ce service n'ait pas lui même des dépendances avec d'autres services.
            // Nous mettons ce service dans la liste des services à vérifier. Il sera analysé dans la prochaine itération de la boucle.
            array_unshift($services, $child_service);
        }
    }

    // À ce stade, tous les évènements automatiques ont été enregistrés.
    // Il reste à traiter les évènements non prévus et au besoin, remettre en route les services isou.
    $services = Service::get_records(array('enable' => true, 'locked' => false, 'plugin' => PLUGIN_ISOU));
    foreach ($services as $service) {
        $event = $service->get_current_event();
        if ($event === false) {
            if ($service->state !== State::OK) {
                $LOGGER->info('   Le service "'.$service->name.'" (id #'.$service->id.') revient à l\'état OK.');

                // Change l'état du service, et clos l'évènement en cours.
                $service->change_state(State::OK);
            }
            continue;
        }

        if ($event->state !== $service->state) {
            $LOGGER->info('   Le service "'.$service->name.'" (id #'.$service->id.') passe de l\'état '.$service->state.' à '.$event->state.'.');

            $service->change_state($event->state);
            continue;
        }

        // On ne traite que les évènements non prévus.
        if ($event->type !== Event::TYPE_UNSCHEDULED) {
            continue;
        }

        // Calcule si un groupe de dépendances dysfonctionne.
        $worst_state = State::OK;
        $groups = Dependency_Group::get_records(array('service' => $event->idservice));
        foreach ($groups as $group) {
            if ($group->is_up() === true) {
                continue;
            }

            $LOGGER->info('      Le groupe "'.$group->name.'" (id #'.$group->id.') dysfonctionne ('.State::$STATES[$group->groupstate].').');

            if ($group->groupstate > $worst_state) {
                $worst_state = $group->groupstate;
            }
        }

        if ($worst_state !== State::OK) {
            // Actualise l'état du service, au besoin.
            if ($service->state !== $worst_state) {
                $service->change_state($worst_state);
            }

            // Si l'évènement est justifié, on continue.
            continue;
        }

        if ($service->state === State::OK) {
            // Si le service a déjà l'état OK, on continue.
            continue;
        }

        $LOGGER->info('   L\'évènement du service "'.$service->name.'" (id #'.$event->id.') a été fermé.');

        // Change l'état du service, et clos l'évènement en cours.
        $service->change_state(State::OK);
    }

    // On met à jour les dates des évènements de type régulier.
    $now = new DateTime();

    $options = array();
    $options['plugin'] = PLUGIN_ISOU;
    $options['has_category'] = true;
    $options['type'] = Event::TYPE_REGULAR;

    $events = Event::get_records($options);
    foreach ($events as $event) {
        if ($event->enddate > $now) {
            continue;
        }

        if (ctype_digit($event->period) === false) {
            continue;
        }

        $event->startdate->add(new DateInterval('PT'.$event->period.'S'));
        $event->enddate->add(new DateInterval('PT'.$event->period.'S'));
        $event->save();
    }

    $LOGGER->info('Fin de la mise à jour de l\'arbre des dépendances');
}

/**
 * Régénère le fichier public isou.json listant les interruptions en cours.
 *
 * @return void
 */
function cron_regenerate_json() {
    $json_data = array();
    $json_data['fisou'] = array();
    $json_data['fisou']['services'] = array();

    $services = Service::get_records(array('plugin' => PLUGIN_ISOU));
    foreach ($services as $service) {
        if ($service->state === State::OK) {
            continue;
        }

        if ($service->state === State::CLOSED) {
            continue;
        }

        if ($service->visible === '0') {
            continue;
        }

        $event = $service->get_current_event();
        if ($event === false) {
            continue;
        }

        if ($event->type === Event::TYPE_REGULAR) {
            continue;
        }

        $data = array();
        $data['id'] = $service->id;
        $data['name'] = $service->name;
        $data['state'] = $service->state;
        $data['date'] = $event->startdate->getTimestamp();
        $data['description'] = explode("\n", $event->description);
        $json_data['fisou']['services'][] = $data;
    }

    $json_data = json_encode($json_data, JSON_PRETTY_PRINT);

    $json_file = PUBLIC_PATH.'/isou.json';
    // Met à jour le fichier uniquement si le contenu est différent.
    if (is_file($json_file) === false || trim(file_get_contents($json_file)) !== trim($json_data)) {
        file_put_contents($json_file, $json_data);
    }
}

/**
 * Envoie une notification des nouveaux évènements en temps réel aux navigateurs des utilisateurs.
 *
 * @return void
 */
function cron_notify() {
    global $CFG, $DB, $LOGGER;

    if ($CFG['notifications_enabled'] === '0') {
        $LOGGER->notice('Les notifications web ne sont pas activées.');
        return;
    }

    if (empty($CFG['site_url']) === true) {
        $LOGGER->notice('Le paramètre "site_url" n\'est pas défini.');
        return;
    }

    $services = Service::get_records(array('plugin' => PLUGIN_ISOU));
    if (isset($services[0]) === false) {
        $LOGGER->notice('Aucun service isou créé.');
        return;
    }

    $messages = array();
    foreach ($services as $service) {
        if (in_array($service->state, array(State::OK, State::CLOSED), $strict = true) === true) {
            // On ignore les retours à la normale et les services fermés.
            continue;
        }

        if ($service->visible === '0') {
            // On ignore les services non visibles.
            continue;
        }

        $event = $service->get_current_event();
        if ($event === false) {
            continue;
        }

        if (in_array($event->type, array(Event::TYPE_REGULAR, Event::TYPE_CLOSED), $strict = true) === true) {
            // On ignore les évènements réguliers et de fermeture.
            continue;
        }

        $format = '%FT%H:%M';
        if (strftime($format, $event->startdate->getTimestamp()) !== strftime($format, TIME)) {
            // On ignore les anciens évènements.
            continue;
        }

        $messages[] = sprintf('%s %s', State::$UNICODE[$event->state], html_entity_decode($service->name, ENT_NOQUOTES, 'UTF-8'));
    }

    $count_services = count($messages);
    if ($count_services === 0) {
        $LOGGER->notice('Aucun nouveau service isou en anomalie.');
        return;
    }

    if ($count_services === 1) {
        $message = sprintf('%d service perturbé - %s :%s%s', $count_services, strftime('%Hh%M'), PHP_EOL, implode(PHP_EOL, $messages));
    } else {
        $message = sprintf('%d services perturbés - %s :%s%s', $count_services, strftime('%Hh%M'), PHP_EOL, implode(PHP_EOL, $messages));
    }

    $subscriptions = Subscription::get_records();
    if (isset($subscriptions[0]) === false) {
        $LOGGER->info('Aucun utilisateur n\'a souscrit aux notifications web');
        return;
    }

    $title = $CFG['site_name'];
    $url = $CFG['site_url'];
    $icon = $CFG['site_url'].'/themes/'.$CFG['theme'].'/favicon.png';

    $notification = new Notification($title, $message, $url, $icon);
    $webpush = $notification->get_webpush();

    $LOGGER->info('Envoi de '.count($subscriptions).' notification(s) web');
    foreach ($subscriptions as $subscription) {
        $response = $subscription->notify($webpush, $notification);

        if ($response->isSubscriptionExpired() === true) {
            $LOGGER->info('Souscription #'.$subscription->id.' expirée pour l\'utilisateur #'.$subscription->iduser);
            $subscription->delete();
        } elseif ($response->isSuccess() === false) {
            $LOGGER->info('Envoi de la souscription #'.$subscription->id.' pour l\'utilisateur #'.$subscription->iduser.' a échoué ('.$response->getReason().')');
        } else {
            $LOGGER->debug('Souscription #'.$subscription->id.' envoyée à l\'utilisateur #'.$subscription->iduser);
        }
    }
}

/**
 * Envoie un rapport quotidien des évènements ayant eu lieu la veille.
 *
 * @return void
 */
function cron_report() {
    global $CFG, $DB, $LOGGER;

    if ($CFG['report_enabled'] === '0') {
        return;
    }

    $now = new DateTime();

    $daily_cron_time = explode(':', $CFG['report_hour']);
    $daily_cron_time = mktime(intval($daily_cron_time[0]), intval($daily_cron_time[1]));

    // Si on n'est pas le même jour que $CFG['last_daily_report'].
    if ($now->format('d') !== $CFG['last_daily_report']->format('d') && $now->getTimestamp() >= $daily_cron_time) {
        // Charge les listes des plugins de monitoring.
        $plugins = array();
        foreach (Plugin::get_records(array('active' => true, 'type' => 'monitoring')) as $plugin) {
            $plugins[$plugin->id] = $plugin->name;
        }

        // Liste des services forcés.
        $closed_services = Service::get_records(array('state' => State::CLOSED));
        $locked_services = Service::get_records(array('locked' => true));

        $visible_isou_services = Service::get_records(array('plugin' => PLUGIN_ISOU, 'has_category' => true, 'visible' => true));

        // Liste des événements.
        $critical_events = array();
        $regular_events = Event::get_records(array('type' => Event::TYPE_REGULAR, 'sort' => array('UPPER(service_name)', 'startdate')));
        $scheduled_events = array();
        $unscheduled_events = array();
        $warning_events = array();

        $events = array();
        foreach (Event::get_records(array('since' => $CFG['last_daily_report'], 'sort' => array('startdate'))) as $event) {
            if (isset($plugins[$event->idplugin]) === false) {
                continue;
            }

            if (in_array($event->type, array(Event::TYPE_REGULAR, Event::TYPE_CLOSED), $strict = true) === true) {
                continue;
            }

            if ($event->idplugin === PLUGIN_ISOU) {
                if (isset($visible_isou_services[$event->idservice]) === false) {
                    continue;
                }

                switch ($event->state) {
                    case State::WARNING:
                        $warning_events[$event->idservice] = $event->idservice;
                        break;
                    case State::CRITICAL:
                        $critical_events[$event->idservice] = $event->idservice;
                        break;
                }

                switch ($event->type) {
                    case Event:: TYPE_SCHEDULED:
                        $scheduled_events[$event->idservice] = $event->idservice;
                        break;
                    case Event::TYPE_UNSCHEDULED:
                        $unscheduled_events[$event->idservice] = $event->idservice;
                        break;
                }
            }

            $plugin = $plugins[$event->idplugin];
            if (isset($events[$plugin]) === false) {
                $events[$plugin] = array();
            }

            $events[$plugin][] = $event;
        }
        ksort($events);

        // TODO: Liste des services supprimés.

        // Envoye la notification.
        if (count($events) !== 0) {
            $smarty = new Smarty();
            $smarty->setTemplateDir(PRIVATE_PATH.'/html/');
            $smarty->setCompileDir(PRIVATE_PATH.'/cache/smarty/');

            $smarty->assign('count_critical_events', count($critical_events));
            $smarty->assign('count_scheduled_events', count($scheduled_events));
            $smarty->assign('count_unscheduled_events', count($unscheduled_events));
            $smarty->assign('count_warning_events', count($warning_events));
            $smarty->assign('events', $events);
            $smarty->assign('last_daily_report', $CFG['last_daily_report']);
            $smarty->assign('closed_services', $closed_services);
            $smarty->assign('locked_services', $locked_services);
            $smarty->assign('regular_events', $regular_events);
            $smarty->assign('STATES', State::get_records());

            $subject = '[isou] Rapport quotidien';
            $message = $smarty->fetch('mail/daily_cron.tpl');

            $additional_headers = '';
            $additional_params = '';
            if (filter_var($CFG['report_sender'], FILTER_VALIDATE_EMAIL) !== false) {
                $additional_headers = 'From: '.$CFG['report_sender'];
                $additional_params = '-f '.$CFG['report_sender'];
            }

            mail($CFG['report_receiver'], $subject, $message, $additional_headers, $additional_params);
        }

        // Met à jour le témoin de dernière notification dans la base de données.
        $sql = "UPDATE configuration SET value=:value WHERE key=:key";
        $query = $DB->prepare($sql);
        $query->execute(array('value' => strftime('%FT%T'), 'key' => 'last_daily_report'));
    }
}

/**
 * Supprime les anciens évènements des plugins autres qu'Isou.
 *
 * @return void
 */
function cron_delete_old_plugin_events() {
    global $LOGGER;

    // On garde les évènements sur 90 jours.
    $expire = strftime('%FT%T', time() - (90 * 24 * 60 * 60));
    $expired_date = new DateTime($expire);

    $services = Service::get_records();
    foreach ($services as $service) {
        if ($service->idplugin === PLUGIN_ISOU) {
            continue;
        }

        foreach ($service->get_all_events() as $event) {
            if ($event->enddate === null) {
                continue;
            }

            if ($event->enddate > $expired_date) {
                continue;
            }

            $LOGGER->info('Supprime l\'évènement #'.$event->id.' du '.$event->startdate->format('Y-m-d\TH:i:s').' au '.$event->enddate->format('Y-m-d\TH:i:s'));
            $event->delete();
        }
    }
}
