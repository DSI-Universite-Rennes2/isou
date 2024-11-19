<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace Isou\Helpers;

use UniversiteRennes2\Isou\Plugin;

/**
 * Classe décrivant les éléments des menus de navigation d'isou.
 */
#[\AllowDynamicProperties]
class SimpleMenu {
    /**
     * Libellé du menu.
     *
     * @var string
     */
    public $label;

    /**
     * Titre de l'onglet.
     *
     * @var string
     */
    public $title;

    /**
     * URL de l'onglet.
     *
     * @var string
     */
    public $url;

    /**
     * Témoin de sélection de l'onglet.
     *
     * @var boolean
     */
    public $selected;

    /**
     * Constructeur de la classe.
     *
     * @param string $label Libellé du menu.
     * @param string $title Titre de l'onglet.
     * @param string|null $url URL de l'onglet.
     * @param boolean $selected Témoin de sélection de l'onglet.
     *
     * @return void
     */
    public function __construct(string $label, string $title, ?string $url = null, bool $selected = false) {
        $this->label = $label;
        $this->title = $title;
        $this->url = $url;
        $this->selected = $selected;
    }

    /**
     * Retourne le menu public.
     *
     * @param boolean $active Témoin indiquant si seuls les menus activés sont retournés.
     *
     * @return array
     */
    public static function get_public_menus(bool $active = true) {
        $plugins = Plugin::get_records(array('active' => $active, 'type' => 'view'));

        $menus = array();
        foreach ($plugins as $plugin) {
            $menu = new SimpleMenu($plugin->settings->label, $title = '', $plugin->settings->route);
            $menu->path = $plugin->codename;

            $menus[$plugin->codename] = $menu;
        }

        return $menus;
    }

    /**
     * Retourne le menu d'administration.
     *
     * @return array
     */
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
