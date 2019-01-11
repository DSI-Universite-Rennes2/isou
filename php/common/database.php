<?php

try {
    if (is_file(substr(DB_PATH, 7)) === false) {
        throw new PDOException(DB_PATH.' n\'existe pas.');
    }
    $DB = new PDO(DB_PATH, '', '');
} catch (PDOException $exception) {
    if (defined('STDIN') === true) {
        echo strftime('%c').': '.$exception->getMessage().PHP_EOL;
        exit(1);
    }

    $smarty->display('common/error_database.tpl');

    $LOGGER->addError($exception->getMessage());

    // Close PDO connection.
    $DB = null;

    exit(1);
}
