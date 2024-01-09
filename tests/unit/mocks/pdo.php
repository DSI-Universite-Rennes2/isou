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
 * Simule la classe PHP PDO.
 */
#[\AllowDynamicProperties]
class PDO extends \PDO {
    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        $this->test_prepare = true;
        $this->test_pdostatement = new PDOStatement();
        $this->test_transaction = false;
    }

    /**
     * Démarre une transaction.
     *
     * @return true
     */
    public function beginTransaction(): bool {
        $this->test_transaction = true;

        return true;
    }

    /**
     * Valide une transaction.
     *
     * @return true
     */
    public function commit(): bool {
        $this->test_transaction = false;

        return true;
    }

    /**
     * Prépare une requête SQL.
     *
     * @param string $sql Requête SQL.
     * @param array $driver_options Options.
     *
     * @return \PDOStatement|false
     */
    public function prepare($sql, $driver_options = null): \PDOStatement|false { // phpcs:ignore
        if ($this->test_prepare === false) {
            return false;
        }

        $this->test_sql = $sql;

        return $this->test_pdostatement;
    }

    /**
     * Détermine si une transaction est en cours.
     *
     * @return boolean
     */
    public function inTransaction(): bool {
        return $this->test_transaction;
    }

    /**
     * Retourne le dernier identifiant inséré.
     *
     * @param string $name Nom.
     *
     * @return string|false
     */
    public function lastInsertId($name = null): string|false { // phpcs:ignore
        return '1';
    }

    /**
     * Annule une transaction.
     *
     * @return true
     */
    public function rollBack(): bool {
        $this->test_transaction = false;
        return true;
    }
}
