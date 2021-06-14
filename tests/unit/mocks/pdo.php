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
     * @return void
     */
    public function beginTransaction() {
        $this->test_transaction = true;
    }

    /**
     * Valide une transaction.
     *
     * @return void
     */
    public function commit() {
        $this->test_transaction = false;
    }

    /**
     * Prépare une requête SQL.
     *
     * @param string $sql Requête SQL.
     * @param array $driver_options Options.
     *
     * @return boolean
     */
    public function prepare($sql, $driver_options = null) { // phpcs:ignore
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
    public function inTransaction() {
        return $this->test_transaction;
    }

    /**
     * Retourne le dernier identifiant inséré.
     *
     * @param string $name Nom.
     *
     * @return integer
     */
    public function lastInsertId($name = null) { // phpcs:ignore Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
        return 1;
    }

    /**
     * Annule une transaction.
     *
     * @return void
     */
    public function rollBack() {
        $this->test_transaction = false;
    }
}
