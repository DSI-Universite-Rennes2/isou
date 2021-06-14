<?php
/**
 * This file is part of isou project.
 *
 * Script exécutant les tests d'intégration.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

require __DIR__.'/../../config.php';
require PRIVATE_PATH.'/libs/cron.php';

$db_file_path = PRIVATE_PATH.'/database/tests.sqlite3';
if (is_file($db_file_path) === true) {
    if (unlink($db_file_path) === false) {
        echo 'Impossible de supprimer le fichier '.$db_file_path.PHP_EOL;
        exit(1);
    }
}

$phinx = new PhinxApplication();
$phinx->setAutoExit(false);

// Initialize database.
$arguments = new StringInput('--environment=tests migrate');
echo PHP_EOL.'* Initialise une base de données de tests'.PHP_EOL;

ob_start();
$result = $phinx->run($arguments, new NullOutput());
ob_end_clean();

if ($result === 0) {
    echo str_repeat(' ', 3).'- OK'.PHP_EOL;
} else {
    echo str_repeat(' ', 3).'- Erreur'.PHP_EOL.PHP_EOL;
    exit(1);
}

// Set UseCases.
$cases_path = __DIR__.'/cases';

$handle = opendir($cases_path);
if ($handle !== false) {
    $scenarios = array();
    while (($entry = readdir($handle)) !== false) {
        if (preg_match('/^scenario([0-9]+)\.json$/', $entry, $matches) !== 1) {
            continue;
        }

        $scenarios[] = $matches[1];
    }
    closedir($handle);

    sort($scenarios);

    foreach ($scenarios as $i) {
        echo '* Charge le Scénario '.$i.PHP_EOL;

        $arguments = new StringInput('--environment=tests --seed=Scenario'.$i.' seed:run');

        ob_start();
        $result = $phinx->run($arguments, new NullOutput());
        ob_end_clean();

        if ($result === 0) {
            echo str_repeat(' ', 3).'- OK'.PHP_EOL;
        } else {
            echo str_repeat(' ', 3).'- Erreur'.PHP_EOL.PHP_EOL;
            exit(1);
        }
    }
}

// Run cases.
echo PHP_EOL;

$DB = new PDO('sqlite:'.$db_file_path, '', '');

// Charge la configuration.
require PRIVATE_PATH.'/libs/configuration.php';
$CFG = get_configurations();

// Charge les plugins.
$plugins = get_plugins();

$cases_dir = __DIR__.'/cases/';

$scenarios = array();

$handle = opendir($cases_dir);
if ($handle !== false) {
    while (($entry = readdir($handle)) !== false) {
        if ($entry[0] === '.') {
            continue;
        }

        $scenarios[] = $entry;
    }

    closedir($handle);
}
sort($scenarios);

$errors_count = 0;
$successes_count = 0;

// Comment: unset($scenarios[0], $scenarios[2]);.
$LOGGER->pushHandler(new \Monolog\Handler\NullHandler());

foreach ($scenarios as $scenario_file) {
    $scenario = file_get_contents($cases_dir.'/'.$scenario_file);
    $scenario = json_decode($scenario);

    if (empty($scenario) === true) {
        echo 'Impossible de lire le fichier '.$scenario_file.PHP_EOL;
        continue;
    }

    echo '* '.$scenario->name.PHP_EOL;
    // Comment: echo '    '.$scenario->description.PHP_EOL;.
    foreach ($scenario->cases as $case) {
        echo str_repeat(' ', 3).'- '.$case->name.' : '.$case->description.PHP_EOL;

        $sql = 'UPDATE services SET state = :state WHERE id = :id';
        $query = $DB->prepare($sql);

        foreach ($case->inputs as $input) {
            $query->execute(array(':state' => $input->state, ':id' => $input->id));
        }

        update_services_tree();

        foreach ($case->outputs as $output) {
            $service = Service::get_record(array('id' => $output->id));

            if ($service->state === $output->state) {
                $state = "\e[0;32m ✔ \e[0m";
                $successes_count++;
            } else {
                $state = "\e[0;31m ✘ \e[0m";
                $errors_count++;
            }

            $event = $service->get_current_event();

            echo str_repeat(' ', 6).$state.' '.$service->name.' : '.State::$STATES[$service->state].PHP_EOL;
            if ($event !== false && empty($event->description) === false) {
                echo str_repeat(' ', 6).$state.' évènement associé : '.implode(', ', explode(PHP_EOL, $event->description)).PHP_EOL;
            }

            // Reset.
            foreach ($scenario->reset as $input) {
                $query->execute(array(':state' => $input->state, ':id' => $input->id));
            }

            update_services_tree();
        }
    }
}

echo PHP_EOL;

if ($errors_count === 0) {
    echo "\e[0;32m ✔ Tests réussis !\e[0m ".$successes_count.'/'.$successes_count.' tests.'.PHP_EOL.PHP_EOL;
} else {
    echo "\e[0;31m ✘ Tests ratés !\e[0m ".$successes_count.'/'.($successes_count + $errors_count).' tests.'.PHP_EOL.PHP_EOL;
    exit(1);
}
