<?php
/**
 * This file is part of isou project.
 *
 * Script exécutant les tests d'intégration.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

// Contrôle que le script est bien exécuté par Composer.
if (getenv('COMPOSER_DEV_MODE') === false) {
    echo 'Le script `tests/demo/run.php` ne peut pas être exécuté directement. Merci d’utiliser la commande `ISOU_ENV=demo composer install` pour exécuter l’environnement de démo d’Isou.'.PHP_EOL;
    exit(1);
}

$environment = getenv('ISOU_ENV');
if ($environment === false || $environment !== 'demo') {
    exit(0);
}

require __DIR__.'/../../config.php';
require PRIVATE_PATH.'/libs/cron.php';

if (is_file(substr(DB_PATH, 7).'.lock') === true) {
    // Note: si le fichier .lock est présent, cela signifie que l'utilisateur ne voulait pas réinitialiser sa base de données.
    exit(0);
}

$phinx = new PhinxApplication();
$phinx->setAutoExit(false);

// Charge le jeu de données de la démo.
echo '* Charge le jeu de données.'.PHP_EOL;

$arguments = new StringInput('--verbose --environment=demo --seed=Demo seed:run');

ob_start();
$result = $phinx->run($arguments, new NullOutput());
ob_end_clean();

if ($result === 0) {
    echo str_repeat(' ', 3).'- OK'.PHP_EOL;
} else {
    echo str_repeat(' ', 3).'- Erreur'.PHP_EOL.PHP_EOL;
    exit(1);
}

echo PHP_EOL;
echo "\e[0;32m ✔ L’environnement de démo est prêt !\e[0m".PHP_EOL.PHP_EOL;
