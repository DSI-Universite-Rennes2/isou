<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
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
    public function error($message, array $context = array()) : void { // phpcs:ignore
    }

    /**
     * Enregistre les messages d'information.
     *
     * @param string $message Message.
     * @param array $context Contexte.
     *
     * @return void
     */
    public function info($message, array $context = array()) : void { // phpcs:ignore
    }

    /**
     * Enregistre les messages d'avertissements.
     *
     * @param string $message Message.
     * @param array $context Contexte.
     *
     * @return void
     */
    public function warning($message, array $context = array()) : void { // phpcs:ignore
    }
}
