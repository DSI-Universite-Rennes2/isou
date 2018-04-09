<?php

use UniversiteRennes2\Isou\Event;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

// TODO:
// - chercher tous les regular_events dépassés et créer un nouvel event avec une period (DateTimeInterval)
// - créer un unscheduled évènement pour chaque backend, sans message de description (INTRODUIRE UN LEFT JOIN ?)
// - comment stocker les descriptions ? JSON ?
// - comment faire revenir les services en vert ? (voir ligne 26)
// mets à jour Isou en fonction des changements d'état des services backend, des évènements prévues, fermés, etc...
function update_services_tree() {
    global $CFG, $LOGGER;

    $services = get_services();

    $LOGGER->addInfo('Mise à jour de l\'arbre des dépendances');

    // check new backend events
    while (current($services) !== false) {
        $parent_service = array_shift($services);
        // TODO: create event
        $parent_service->set_reverse_dependencies($parent_service->state);

        $LOGGER->addInfo('Recherche des dépendances pour le service '.$parent_service->name.' (id #'.$parent_service->id.')');
        $LOGGER->addInfo('   '.count($parent_service->reverse_dependencies).' groupes dépendent du service "'.$parent_service->name.'" (avec l\'état: '.$parent_service->state.')');

        if ($parent_service->state == State::OK) {
            // tous les services ISOU dépendant de ce service
            // $check_isou_services après cette boucle
        }
        // } else { ...
        foreach ($parent_service->reverse_dependencies as $dependencies_group) {
            $child_service = get_service(array('enabled' => true, 'id' => $dependencies_group->idservice));

            if ($child_service === false) {
                $LOGGER->addError('   Le service avec l\'id #'.$dependencies_group->idservice.' n\'existe pas.'.
                    ' Il est pourtant lié avec le service "'.$parent_service->name.'" (id #'.$parent_service->id.') dans le groupe "'.$dependencies_group->name.'" (id #'.$dependencies_group->id.')');
                continue;
            }

            if ($child_service->locked === '1') {
                // do nothing ; service is locked
                $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') est actuellement en mode forcé. Il ne peut pas être mis à jour.');
                continue;
            }

            $event = $child_service->get_current_event();

            // if($event !== FALSE && in_array($event->type, array(Event::TYPE_REGULAR, Event::TYPE_CLOSED))){
            if ($event !== false && in_array($event->type, array(Event::TYPE_REGULAR), true) === true) {
                // do nothing ;  service in maintenance or closed
                $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') a actuellement une interruption régulière en cours ou est fermé. Il ne peut pas être mis à jour.');
                continue;
            }

            $LOGGER->addInfo('   Analyse du groupe "'.$dependencies_group->name.'" (id #'.$dependencies_group->id.') attaché au service "'.$child_service->name.'" (id #'.$child_service->id.')');

            if ($dependencies_group->redundant === '0') {
                // Groupe de services non-redondés.
                if ($child_service->state <= $dependencies_group->groupstate) {
                    // change service state.
                    if ($child_service->state !== $dependencies_group->groupstate) {
                        // change l'état du service et ajoute un évènement au besoin
                        $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') passe de l\'état '.$child_service->state.' à '.$dependencies_group->groupstate.'.');
                        $child_service->change_state($dependencies_group->groupstate);
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } else {
                // Groupe de services redondés.
                // TODO: add array to don't check X times the same redundant group
                // look other group members
                $redundant_services = get_services_by_dependencies_group($dependencies_group->id);
                $state = State::UNKNOWN;
                foreach ($redundant_services as $redundant_service) {
                    // find the best service status (hope to find State::OK)
                    if ($state > $redundant_service->state) {
                        $state = $redundant_service->state;
                    }
                }

                if (in_array($state, array(State::OK, State::CLOSED), true)) {
                    // do nothing, at least one server up !
                    continue;
                }

                // update service
                if ($child_service->state < $state) {
                    $LOGGER->addInfo('   Le service "'.$child_service->name.'" (id #'.$child_service->id.') passe de l\'état '.$child_service->state.' à '.$dependencies_group->groupstate.'.');
                    $child_service->change_state($state);
                    // update_services_tree(array($child_service));
                    // array_unshift($services, $child_service);
                    // $child_service->state = $dependencies_group->groupstate;
                    // save service...
                } else {
                    continue;
                }
            }

            // create event.
            if ($event === false) {
                $LOGGER->addInfo('Création d\'un évènement pour le service "'.$child_service->name.'" (id #'.$child_service->id.').');

                $event = new Event();
                $event->state = $child_service->state;
                $event->type = Event::TYPE_UNSCHEDULED;
                $event->idservice = $child_service->id;
            } else {
                $LOGGER->addInfo('Un évènement existe déjà pour le service "'.$child_service->name.'" (id #'.$child_service->id.').');
            }

            // update event message.
            // si un évènement a été créé précédemment, on met à jour le message de l'évènement si nécessaire
            $message = get_dependency_message($dependencies_group->idmessage);
            if (empty($event->description) === true) {
                $event->set_description($message->message, 1);
                $event->save();
            } elseif (stripos($event->description, $message->message) === false) {
                $event->set_description($event->description."\n".$message->message, 1);
                $event->save();
            }

            // vérifions que ce service n'ait pas lui même des dépendances avec d'autres services
            // update_services_tree(array($child_service));
            array_unshift($services, $child_service);
        }
    }

    // check current events
    $events = get_events(array('plugin' => PLUGIN_ISOU, 'finished' => false));
    foreach ($events as $event) {
        $error = false;

        $groups = get_dependency_groups(array('service' => $event->idservice));
        foreach ($groups as $group) {
            if ($group->is_up() === false) {
                $error = true;
                break;
            }
        }

        if ($error === false) {
            $service = get_service(array('enable' => true, 'id' => $event->idservice, 'locked' => false));
            if ($service !== false) {
                $LOGGER->addInfo('   L\'évènement du service "'.$service->name.'" (id #'.$event->id.') a été fermé.');
                $service->change_state(State::OK);
                $event->close();
            }
        }
    }

    $LOGGER->addInfo('Fin de la mise à jour de l\'arbre des dépendances');
}

function cron_regenerate_json() {
    $json_data = array();
    $json_data['fisou'] = array();
    $json_data['fisou']['services'] = array();

    $services = get_services(array('plugin' => PLUGIN_ISOU));
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
    if (!is_file($json_file) || trim(file_get_contents($json_file)) !== trim($json_data)) {
        file_put_contents($json_file, $json_data);
    }
}

function cron_notify() {
    global $DB, $LOGGER;

    $now = new DateTime();

    $daily_cron_time = explode(':', $CFG['daily_cron_hour']);
    $daily_cron_time = mktime($daily_cron_time[0], $daily_cron_time[1]);

    // si on n'est pas le même jour que $CFG['last_daily_cron_update']
    if ($now->format('d') != $CFG['last_daily_cron_update']->format('d') && $now->getTimestamp() >= $daily_cron_time) {
        // Liste des services forcés
        $services = get_services(array('locked' => true));

        // Liste des événements
        $events = get_events(array('since' => $now));

        // Liste des services supprimés
        // TODO
        // TODO: send mail
        $sql = "UPDATE configuration SET value=? WHERE key=?";
        $query = $DB->prepare($sql);
        $query->execute(array(strftime('%FT%T'), 'last_daily_cron_update'));
    }
}
