<?php

namespace Isou\Helpers;

use UniversiteRennes2\Isou\Plugin;

class SimpleMenu {
    public $label;
    public $title;
    public $url;
    public $selected;

    public function __construct($label, $title, $url = null, $selected = false) {
        $this->label = $label;
        $this->title = $title;
        $this->url = $url;
        $this->selected = $selected;
    }

    public static function get_public_menus($active = true) {
        $plugins = Plugin::get_records(array('active' => $active, 'type' => 'view'));

        $menus = array();
        foreach ($plugins as $plugin) {
            $menu = new SimpleMenu($plugin->settings->label, $title = '', $plugin->settings->route);
            $menu->path = $plugin->codename;

            $menus[$plugin->codename] = $menu;
        }

        return $menus;
    }

    public static function get_adminitration_menus() {
        $menus = array();
        $menus['evenements'] = new SimpleMenu('évènements', 'ajouter un évenement', 'evenements');
        $menus['annonce'] = new SimpleMenu('annonce', 'ajouter une annonce', 'annonce');
        $menus['statistiques'] = new SimpleMenu('statistiques', 'afficher les statistiques', 'statistiques');
        $menus['services'] = new SimpleMenu('services', 'ajouter un service', 'services');
        $menus['dependances'] = new SimpleMenu('dépendances', 'ajouter une dépendance', 'dependances');
        $menus['categories'] = new SimpleMenu('catégories', 'ajouter une catégorie', 'categories');
        $menus['configuration'] = new SimpleMenu('configuration', 'configurer l\'application', 'configuration');
        $menus['aide'] = new SimpleMenu('aide', 'afficher l\'aide', 'aide');

        return $menus;
    }
}
