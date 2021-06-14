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
 * Simule la classe PHP PDOStatement.
 */
class PDOStatement extends \PDOStatement {
    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        $this->test_execute = true;
        $this->test_fetch = true;
        $this->test_fetch_class = false;
        $this->test_fetch_all = array();
    }

    /**
     * Retourne les infos de la dernière erreur.
     *
     * @return array
     */
    public function errorInfo() {
        return array();
    }

    /**
     * Exécute une requête SQL préparée.
     *
     * @param array $options Options.
     *
     * @return boolean
     */
    public function execute($options = null) { // phpcs:ignore
        if ($this->test_execute === false) {
            return false;
        }

        return true;
    }

    /**
     * Retourne un enregistrement.
     *
     * @param integer $fetch_style Style.
     * @param integer $cursor_orientation Orientation.
     * @param integer $cursor_offset Décalage.
     *
     * @return array|false
     */
    public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0) { // phpcs:ignore
        if ($this->test_fetch === false) {
            return false;
        }

        return $this->test_fetch_class;
    }

    /**
     * Retourne des enregistrements.
     *
     * @param integer $fetch_style Mode.
     * @param string $classname Nom de la classe.
     * @param array $constructorArgs Paramètres du constructeur.
     *
     * @return array
     */
    public function fetchAll($fetch_style = null, $classname = null, $constructorArgs = array()) { // phpcs:ignore
        return $this->test_fetch_all;
    }

    /**
     * Définit le mode pour récupérer les enregistrements.
     *
     * @param integer $mode Mode.
     * @param string $classname Nom de la classe.
     * @param array $constructorArgs Paramètres du constructeur.
     *
     * @return void
     */
    public function setFetchMode($mode = \PDO::FETCH_CLASS, $classname = '', $constructorArgs = array()) { // phpcs:ignore
        if ($mode === \PDO::FETCH_CLASS) {
            $this->test_fetch_class = new $classname;
        }
    }
}
