<?php

/**
  * Fonctions liées aux procédures du crontab.
  */

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Dependency_Message;
use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

/**
  * Mets à jour Isou en fonction des changements d'état des services backend, des évènements prévues, fermés, etc...
  *
  * @return void
  */
function update_services_tree() {
    global $CFG, $LOGGER;

    // TODO: utilisé la propriété timemodified.
    $services = Service::get_records();

    $LOGGER->addInfo('Mise à jour de l\'arbre des dépendances');

    // Vérifie tous les services.
    while (current($services) !== false) {
        $parent_service = array_shift($services);

        // Définis tous les enfants de ce service.
        $parent_service->set_reverse_dependencies($parent_service->state);

        $LOGGER->addInfo('Recherche des dépendances pour le service '.$parent_service->name.' (id #'.$parent_service->id.')');
        $LOGGER->addInfo('   '.count($parent_service->reverse_dependencies).' groupes dépendent du service "'.$parent_service->name.'" (avec l\'état: '.$parent_service->state.')');

        // Parcours chaque enfant.
        foreach ($parent_service->reverse_dependencies as $dependencies_group) {
            $child_service = Service::get_record(array('id' => $dependencies_group->idservice, 'enable' => true));

            // Si l'enfant n'existe plus ou n'est plus actif, on ne fait rien.
            if ($child_service === false) {
                $LOGGER->addError('   Le service avec l\'id #'.$dependencies_group->idservice.' n\'existe pas.'.
                    ' Il est pourtant lié avec le service "'.$parent_service->name.'" (id #'.$parent_service->id.') dans le groupe "'.$dependencies_group->name.'" (id #'.$dependencies_group->id.')');
                continue;
            }

            // Si le service enfant est verrouillé, on ne fait rien.
            if ($child_service->locked === '1') {
                $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') est actuellement en mode forcé. Il ne peut pas être mis à jour.');
                continue;
            }

            // On récupère l'évènement en cours de l'enfant.
            $event = $child_service->get_current_event();

            // Si il y a un évènement en cours de type régulier ou de fermeture, on ne fait rien.
            if ($event !== false && in_array($event->type, array(Event::TYPE_REGULAR, Event::TYPE_CLOSED), $strict = true) === true) {
                $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') a actuellement une interruption régulière en cours ou est fermé. Il ne peut pas être mis à jour.');
                continue;
            }

            $LOGGER->addInfo('   Analyse du groupe "'.$dependencies_group->name.'" (id #'.$dependencies_group->id.') attaché au service "'.$child_service->name.'" (id #'.$child_service->id.')');

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

                if (in_array($state, array(State::OK, State::CLOSED), $strict = true) === true) {
                    // Un service semble fonctionné dans le groupe redondé. Il n'y a donc pas de problèmes.
                    continue;
                }
            } else {
                // Si le goupe n'est pas redondé... on prend l'état du groupe de dépendances.
                $state = $dependencies_group->groupstate;
            }

            if ($child_service->state < $state) {
                // Le niveau d'état du groupe est plus important que l'état actuel du service.
                // On change l'état du service et ajoute un évènement au besoin.
                $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') passe de l\'état '.$child_service->state.' à '.$dependencies_group->groupstate.'.');
                $event = $child_service->change_state($state);
            } else {
                // Ce groupe de dépendance n'a pas de problèmes. On continue à parcourir les autres groupes de dépendances.
                continue;
            }

            // Si un évènement a été créé précédemment, on met à jour le message de l'évènement si nécessaire.
            $message = Dependency_Message::get_record(array('id' => $dependencies_group->idmessage));
            if (empty($event->description) === true) {
                $event->set_description($message->message, 1);
                $event->save();
            } elseif (stripos($event->description, $message->message) === false) {
                $event->set_description($event->description."\n".$message->message, 1);
                $event->save();
            }

            // Vérifions que ce service n'ait pas lui même des dépendances avec d'autres services.
            // Nous mettons ce service dans la liste des services à vérifier. Il sera analysé dans la prochaine itération de la boucle.
            array_unshift($services, $child_service);
        }
    }

    // Vérifie les évènements Isou non terminés.
    $events = Event::get_records(array('plugin' => PLUGIN_ISOU, 'finished' => false, 'type' => Event::TYPE_UNSCHEDULED));
    foreach ($events as $event) {
        $error = false;

        $groups = Dependency_Group::get_records(array('service' => $event->idservice));
        foreach ($groups as $group) {
            if ($group->is_up() === false) {
                $error = true;
                break;
            }
        }

        // Si il n'y a pas de services en erreur dans les dépendances, on peut tenter de fermer l'évènement.
        if ($error === false) {
            $service = Service::get_record(array('id' => $event->idservice, 'enable' => true, 'locked' => false, 'plugin' => PLUGIN_ISOU));
            if ($service !== false) {
                $LOGGER->addInfo('   L\'évènement du service "'.$service->name.'" (id #'.$event->id.') a été fermé.');
                $service->change_state(State::OK);
            }
        }
    }

    // Ajoute le témoin de fermeture lorsqu'un évènement de fermeture démarre.
    $events = Event::get_records(array('plugin' => PLUGIN_ISOU, 'type' => Event::TYPE_CLOSED));
    foreach ($events as $event) {
        if ($event->is_now() === false) {
            continue;
        }

        $service = Service::get_record(array('id' => $event->idservice, 'locked' => false, 'plugin' => PLUGIN_ISOU));
        if ($service !== false && $service->state !== State::CLOSED) {
            $LOGGER->addInfo('   Le service "'.$service->name.'" (id #'.$service->id.') passe de l\'état '.$service->state.' à '.State::CLOSED.'.');
            $event = $service->change_state(State::CLOSED);
        }
    }

    $LOGGER->addInfo('Fin de la mise à jour de l\'arbre des dépendances');
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

        $data = array();
        $data['name'] = $service->name;
        $data['state'] = $service->state;
        $data['date'] = $event->startdate->getTimestamp();
        $data['description'] = explode("\n", $event->description);
        $json_data['fisou']['services'][] = $data;
    }

    $json_data = json_encode($json_data, JSON_PRETTY_PRINT);

    $json_file = PUBLIC_PATH.'/isou.json';
    // Mets à jour le fichier uniquement si le contenu est différent.
    if (is_file($json_file) === false || trim(file_get_contents($json_file)) !== trim($json_data)) {
        file_put_contents($json_file, $json_data);
    }
}

/**
  * Envoie une notification quotidienne des évènements ayant eu lieu.
  *
  * @return void
  */
function cron_notify() {
    global $DB, $LOGGER;

    $now = new DateTime();

    $daily_cron_time = explode(':', $CFG['daily_cron_hour']);
    $daily_cron_time = mktime($daily_cron_time[0], $daily_cron_time[1]);

    // Si on n'est pas le même jour que $CFG['last_daily_cron_update'].
    if ($now->format('d') !== $CFG['last_daily_cron_update']->format('d') && $now->getTimestamp() >= $daily_cron_time) {
        // Liste des services forcés.
        $services = Service::get_records(array('locked' => true));

        // Liste des événements.
        $events = Event::get_records(array('since' => $now));

        // TODO: Liste des services supprimés.
        // TODO: Envoyer la notification.

        // Mets à jour le témoin de dernière notification dans la base de données.
        $sql = "UPDATE configuration SET value=? WHERE key=?";
        $query = $DB->prepare($sql);
        $query->execute(array(strftime('%FT%T'), 'last_daily_cron_update'));
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

            $LOGGER->addInfo('Supprime l\'évènement #'.$event->id.' du '.$event->startdate->format('Y-m-d\TH:i:s').' au '.$event->enddate->format('Y-m-d\TH:i:s'));
            $event->delete();
        }
    }
}
