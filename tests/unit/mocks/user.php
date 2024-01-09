<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Mock;

/**
 * Simule un utilisateur authentifié.
 */
#[\AllowDynamicProperties]
class User {
    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        $this->id = 1;
        $this->authentification = 'manual';
        $this->username = 'isou';
        $this->password = '';
        $this->firstname = '';
        $this->lastname = 'Misou-Mizou';
        $this->email = '';
        $this->admin = 1;
        $this->lastaccess = null;
        $this->timecreated = date('Y-m-d\TH:i:s');
    }
}
