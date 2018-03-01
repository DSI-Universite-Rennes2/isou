<?php

/**
 * Fonctions liées aux procédures de mise à jour.
 *
 */

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Procède à la migration de la version 1.0.0 (2013-00-00.1) à la version 2.0.0.
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function upgrade_100_to_200() {
    echo 'Mise à jour de la version 1.0.0 (2013-00-00.1). vers la version 2.0.0.'.PHP_EOL;

    $phinx = new PhinxApplication();
    $phinx->setAutoExit(false);

    $arguments = new StringInput('--verbose --environment=production --target=20160110000001 migrate');
    if ($phinx->run($arguments, new NullOutput()) !== 0) {
        throw new Exception('Une erreur est survenue lors de la mise à jour vers la version 2.0.0.');
    }
}

/**
 * Procède à la migration de la version 0.11.0 (2012-03-16.1) à la version 1.0.0 (2013-00-00.1).
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function upgrade_0110_to_100() {
    echo 'Mise à jour de la version 0.11.0 (2012-03-16.1) vers la version 1.0.0 (2013-00-00.1).'.PHP_EOL;

    throw new Exception('Not implemented. TODO...');
}

/**
 * Procède à la migration de la version 0.10.0 (2012-02-16.1) à la version 0.11.0 (2012-03-16.1).
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function upgrade_0100_to_0110() {
    echo 'Mise à jour de la version 0.10.0 (2012-02-16.1) vers la version 0.11.0 (2012-03-16.1).'.PHP_EOL;

    throw new Exception('Not implemented. TODO...');
}

/**
 * Procède à la migration de la version 0.9.6 à la version 0.10.0 (2012-02-16.1).
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function upgrade_096_to_0100() {
    echo 'Mise à jour de la version 0.9.6 vers la version 0.10.0 (2012-02-16.1).'.PHP_EOL;

    throw new Exception('Not implemented. TODO...');
}

/**
 * Procède à la migration de la version 0.9.5 à la version 0.9.6.
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function upgrade_095_to_096() {
    echo 'Mise à jour de la version 0.9.5 vers la version 0.9.6.'.PHP_EOL;

    throw new Exception('Not implemented. TODO...');
}

/**
 * Procède à la migration de la version 0.9.0 à la version 0.9.5.
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function upgrade_090_to_095() {
    echo 'Mise à jour de la version 0.9.0 vers la version 0.9.5.'.PHP_EOL;

    throw new Exception('Not implemented. TODO...');
}

/**
 * Initialise la création de la base de données.
 *
 * @throws Exception if any errors occur.
 *
 * @return void
 */
function initialize_phinx() {
    echo 'Initialise la base de donnnées'.PHP_EOL;

    $phinx = new PhinxApplication();
    $phinx->setAutoExit(false);

    $arguments = new StringInput('--verbose --environment=production migrate');
    if ($phinx->run($arguments, new NullOutput()) !== 0) {
        throw new Exception('Une erreur est survenue lors de l\'initialisation de la base de données.');
    }
}
