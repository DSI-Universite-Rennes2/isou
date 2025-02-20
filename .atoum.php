<?php
/**
 * This file is part of isou project.
 *
 * This file will automatically be included before EACH test if -bf/--bootstrap-file argument is not used.
 *
 * Use it to initialize the tested code, add autoloader, require mandatory file, or anything that needs to be done before EACH test.
 *
 * More information on documentation:
 * - [en] http://docs.atoum.org/en/chapter3.html#Bootstrap-file
 * - [fr] http://docs.atoum.org/fr/chapter3.html#Fichier-de-bootstrap
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

// Autoload composer.
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/tests/unit/mocks/logger.php';
require_once __DIR__.'/tests/unit/mocks/pdo.php';
require_once __DIR__.'/tests/unit/mocks/pdostatement.php';
require_once __DIR__.'/tests/unit/mocks/user.php';

define('PLUGIN_ISOU', '1');
define('PRIVATE_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__.'/www');
define('URL', '');

// TODO: refactoriser pour supprimer ces variables.
define('TIME', time());
define('STR_TIME', date('Y-m-d\TH:i', TIME));
