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
 * Simule la classe PHP PDOStatement.
 */
#[\AllowDynamicProperties]
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
    public function errorInfo(): array {
        return array();
    }

    /**
     * Exécute une requête SQL préparée.
     *
     * @param array $options Options.
     *
     * @return boolean
     */
    public function execute($options = null): bool { // phpcs:ignore
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
     * @return mixed
     */
    public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0): mixed { // phpcs:ignore
        if ($this->test_fetch === false) {
            return false;
        }

        return $this->test_fetch_class;
    }

    /**
     * Retourne des enregistrements.
     *
     * @param integer $fetch_style Mode.
     * @param mixed ...$args Divers arguments attendus en fonction du mode utilisé.
     *
     * @return array
     */
    public function fetchAll(int $fetch_style = null, mixed ...$args): array { // phpcs:ignore
        return $this->test_fetch_all;
    }

    /**
     * Définit le mode pour récupérer les enregistrements.
     *
     * @param integer $mode Mode.
     * @param mixed ...$args Divers arguments attendus en fonction du mode utilisé.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return void
     */
    public function setFetchMode(int $mode = \PDO::FETCH_CLASS, mixed ...$args) { // phpcs:ignore
        if ($mode === \PDO::FETCH_CLASS) {
            if (isset($args[0]) === false) {
                throw new \Exception(__CLASS__.'::'.__METHOD__.' expects at least 1 argument, 0 given');
            }

            $classname = $args[0];
            $this->test_fetch_class = new $classname;
        }
    }
}
