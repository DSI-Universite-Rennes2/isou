<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Dependency_Group;
use UniversiteRennes2\Isou\Dependency_Group_Content;
use UniversiteRennes2\Isou\Plugin;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

/**
 * Fonction permettant de mettre à jour les groupements de services par nom de domaine.
 *
 * @return void
 */
function plugin_isou_update_grouping() {
    global $LOGGER;

    $plugin = Plugin::get_record(array('id' => PLUGIN_ISOU));
    if ($plugin->settings->grouping === false) {
        return;
    }

    $grouping = array();

    foreach (Service::get_records() as $service) {
        if ($service->idplugin === PLUGIN_ISOU) {
            continue;
        }

        $parts = explode('@', $service->name);

        $host = end($parts);

        $grouping[$host][] = $service->id;
    }

    foreach ($grouping as $hostname => $grouping_dependencies) {
        $service = Service::get_record(array('name' => $hostname, 'plugin' => PLUGIN_ISOU, 'has_category' => false));
        if ($service === false) {
            // Enregistre un nouveau service au besoin.
            $service = new Service();
            $service->idplugin = PLUGIN_ISOU;
            $service->idcategory = null;
            $service->name = $hostname;
            $service->visible = 0;
            $service->locked = 0;
            $service->state = 0;

            $result = $service->save();
            if (isset($result['errors'][0]) === true) {
                continue;
            }
        }

        $dependencies = Dependency_Group::get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($service->id);
        foreach (array(State::WARNING, State::CRITICAL) as $state) {
            if (isset($dependencies[$state]) === true) {
                // On passe à l'état suivant.
                continue;
            }

            $dependency_group = new Dependency_Group();
            $dependency_group->idservice = $service->id;
            $dependency_group->name = 'Groupe de dépendances non redondés';
            $dependency_group->redundant = 0;
            $dependency_group->groupstate = $state;
            $dependency_group->message = '';
            $result = $dependency_group->save();
            if (isset($result['errors'][0]) === true) {
                $LOGGER->error('Impossible de créer un groupe de dépendances avec l\'état '.$state.' pour le service #'.$service->id);

                // On passe au groupement suivant.
                break 2;
            }

            $dependency_group->set_contents();
            $dependencies[$state][$dependency_group->id] = $dependency_group;
        }

        foreach ($dependencies as $state => $dependency_groups) {
            $grouping_dependencies_list = $grouping_dependencies;
            foreach ($dependency_groups as $groupid => $dependency_group) {
                foreach ($dependency_group->contents as $content) {
                    $index = array_search($content->idservice, $grouping_dependencies_list, $strict = true);
                    if ($index === false) {
                        // Supprime d'anciens services.
                        $LOGGER->info('Supprime du groupe #'.$groupid.' le service #'.$content->idservice);

                        $content->delete();
                        continue;
                    }

                    unset($grouping_dependencies_list[$index]);
                }

                foreach ($grouping_dependencies_list as $idservice) {
                    // Lie le service représentant un hostname avec de nouveaux services.
                    $dependency_group_content = new Dependency_Group_Content();
                    $dependency_group_content->idgroup = $groupid;
                    $dependency_group_content->idservice = $idservice;
                    $dependency_group_content->servicestate = $state;

                    $result = $dependency_group_content->save();
                    if (isset($result['errors'][0]) === true) {
                        $LOGGER->error('Impossible d\'ajouter au groupe #'.$groupid.' le service #'.$idservice);
                    } else {
                        $LOGGER->info('Ajoute au groupe #'.$groupid.' le service #'.$idservice);
                    }
                }
            }
        }
    }
}
