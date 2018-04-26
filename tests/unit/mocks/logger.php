<?php
/*
 * This file is part of Isou project.
 *
 * (c) Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UniversiteRennes2\Mock;

/**
 * Classe pour simuler la classe Monolog\Logger.
 */
class Logger extends \Monolog\Logger {
    public function __construct($name = '') {
    }

    public function addError($message, array $context = array()) {
    }

    public function addInfo($message, array $context = array()) {
    }

    public function addWarning($message, array $context = array()) {
    }
}
