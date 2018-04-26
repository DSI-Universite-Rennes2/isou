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
 * Classe pour simuler la classe PHP PDO.
 */
class PDO extends \PDO {

    public function __construct() {
        $this->test_prepare = true;
        $this->test_pdostatement = new PDOStatement();
        $this->test_transaction = false;
    }

    public function beginTransaction() {
        $this->test_transaction = true;
    }

    public function commit() {
        $this->test_transaction = false;
    }

    public function prepare($sql, $driver_options = null) {
        if ($this->test_prepare === false) {
            return false;
        }

        $this->test_sql = $sql;

        return $this->test_pdostatement;
    }

    public function errorInfo() {
        /*
        try {
            parent::errorInfo();
        } catch (Exception $exception) {
            return $exception;
        }*/
    }

    public function inTransaction() {
        return $this->test_transaction;
    }

    public function lastInsertId($name = null) {
        return 1;
    }

    public function rollBack() {
        $this->test_transaction = false;
    }
}
