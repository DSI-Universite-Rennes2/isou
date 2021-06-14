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
 * Simule la classe Monolog\Logger.
 */
class Logger extends \Monolog\Logger {
    /**
     * Constructeur de la classe.
     *
     * @param string $name Nom du logger.
     *
     * @return void
     */
    public function __construct(string $name = '') {
    }

    /**
     * Enregistre les messages d'erreurs.
     *
     * @param string $message Message.
     * @param array $context Contexte.
     *
     * @return void
     */
    public function addError($message, array $context = array()) { // phpcs:ignore
    }

    /**
     * Enregistre les messages d'information.
     *
     * @param string $message Message.
     * @param array $context Contexte.
     *
     * @return void
     */
    public function addInfo($message, array $context = array()) { // phpcs:ignore
    }

    /**
     * Enregistre les messages d'avertissements.
     *
     * @param string $message Message.
     * @param array $context Contexte.
     *
     * @return void
     */
    public function addWarning($message, array $context = array()) { // phpcs:ignore
    }
}
