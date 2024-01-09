<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

try {
    if (is_file(substr(DB_PATH, 7)) === false) {
        throw new PDOException(DB_PATH.' n\'existe pas.');
    }
    $DB = new PDO(DB_PATH, '', '', array(PDO::ATTR_STRINGIFY_FETCHES => 1));
} catch (PDOException $exception) {
    if (defined('STDIN') === true) {
        echo date('r').': '.$exception->getMessage().PHP_EOL;
        exit(1);
    }

    $smarty->display('common/error_database.tpl');

    $LOGGER->error($exception->getMessage());

    // Close PDO connection.
    $DB = null;

    exit(1);
}
