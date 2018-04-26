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
 * Classe pour simuler la classe PHP PDOStatement.
 */
class PDOStatement extends \PDOStatement {

    public function __construct() {
        $this->test_execute = true;
        $this->test_fetch = true;
        $this->test_fetch_class = false;
        $this->test_fetch_all = array();
    }

    public function errorInfo() {
        return array();
    }

    public function execute($options = null) {
        if ($this->test_execute === false) {
            return false;
        }

        return true;
    }

    public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
        if ($this->test_fetch === false) {
            return false;
        }

        return $this->test_fetch_class;
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = array()) {
        return $this->test_fetch_all;
    }

    public function setFetchMode($mode = \PDO::FETCH_CLASS, $classname = '', $ctorargs = array()) {
        if ($mode === \PDO::FETCH_CLASS) {
            $this->test_fetch_class = new $classname;
        }
    }
}
