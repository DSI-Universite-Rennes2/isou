<?php

/*
This file will automatically be included before EACH test if -bf/--bootstrap-file argument is not used.

Use it to initialize the tested code, add autoloader, require mandatory file, or anything that needs to be done before EACH test.

More information on documentation:
[en] http://docs.atoum.org/en/chapter3.html#Bootstrap-file
[fr] http://docs.atoum.org/fr/chapter3.html#Fichier-de-bootstrap
*/

// Autoload composer.
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/tests/unit/mocks/logger.php';
require_once __DIR__.'/tests/unit/mocks/pdo.php';
require_once __DIR__.'/tests/unit/mocks/pdostatement.php';

/*
$testGenerator = new atoum\test\generator();
$testGenerator->setTestClassesDirectory(__DIR__.'/tests/unit/classes');
$testGenerator->setTestClassNamespace('UniversiteRennes2\Isou\tests\unit');
$testGenerator->setTestedClassesDirectory(__DIR__.'/classes/isou');
$testGenerator->setTestedClassNamespace('UniversiteRennes2\Isou');

$script->getRunner()->setTestGenerator($testGenerator);
 */
define('PLUGIN_ISOU', '1');
define('URL', '');

// TODO: refactoriser pour supprimer ces variables.
$_SESSION['phpCAS']['user'] = '';
define('TIME', time());
define('STR_TIME', strftime('%Y-%m-%dT%H:%M', TIME));
