<?php

/**
 * Script executant les tests d'intégration.
 *
 */

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use UniversiteRennes2\Isou\Service;
use UniversiteRennes2\Isou\State;

require __DIR__.'/../../config.php';
require PRIVATE_PATH.'/libs/cron.php';
require PRIVATE_PATH.'/libs/events.php';
require PRIVATE_PATH.'/libs/events_descriptions.php';
require PRIVATE_PATH.'/libs/dependencies.php';
require PRIVATE_PATH.'/libs/states.php';
require PRIVATE_PATH.'/libs/services.php';

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
for ($i = 1 ; $i <= 3 ; $i++) {
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

// Run cases.
echo PHP_EOL;

$DB = new PDO('sqlite:'.$db_file_path, '', '');

$sql = "SELECT key, value FROM configuration";
$CFG = array();
if($query = $DB->query($sql)){
    while($config = $query->fetch(PDO::FETCH_OBJ)){
        if(in_array($config->key, array('authentification_cas_admin_usernames', 'notification_receivers'))){
             $CFG[$config->key] = json_decode($config->value);
        }else{
            $CFG[$config->key] = $config->value;
        }
    }
}

$cases_dir = __DIR__.'/cases/';

$scenarios = array();
if ($handle = opendir($cases_dir)) {
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

// unset($scenarios[0], $scenarios[2]);
$LOGGER->pushHandler(new \Monolog\Handler\NullHandler());

foreach ($scenarios as $scenario_file) {
    $scenario = file_get_contents($cases_dir.'/'.$scenario_file);
    $scenario = json_decode($scenario);

    if (empty($scenario) === true) {
        echo 'Impossible de lire le fichier '.$scenario_file.PHP_EOL;
        continue;
    }

    echo '* '.$scenario->name.PHP_EOL;
    // echo '    '.$scenario->description.PHP_EOL;
    foreach ($scenario->cases as $case) {
        echo str_repeat(' ', 3).'- '.$case->name.PHP_EOL;
        // echo str_repeat(' ', 3).$case->description.PHP_EOL;

        $sql = 'UPDATE services SET state = :state WHERE id = :id';
        $query = $DB->prepare($sql);

        foreach ($case->inputs as $input) {
            $query->execute(array(':state' => $input->state, ':id' => $input->id));
        }

        update_services_tree(get_services(array('type' => Service::TYPE_SHINKEN_THRUK)));

        foreach ($case->outputs as $output) {
            $service = get_service(array('id' => $output->id));

            if ($service->state === $output->state) {
                echo str_repeat(' ', 6).'✔ ';
                $successes_count++;
            } else {
                echo str_repeat(' ', 6).'✘ ';
                $errors_count++;
            }

            $event = ($service->get_current_event() !== false);

            echo $service->name.' : '.State::$STATES[$service->state].' ('.intval($event).' event)'.PHP_EOL;

            // Reset.
            foreach ($scenario->reset as $input) {
                $query->execute(array(':state' => $input->state, ':id' => $input->id));
            }
            update_services_tree(get_services(array('type' => Service::TYPE_SHINKEN_THRUK)));
        }
    }
}

echo PHP_EOL;

if ($errors_count === 0) {
    echo '✔ Tests réussis ! '.$successes_count.'/'.$successes_count.' tests.'.PHP_EOL.PHP_EOL;
} else {
    echo '✘ Tests ratés ! '.$successes_count.'/'.($successes_count + $errors_count).' tests.'.PHP_EOL.PHP_EOL;
    exit(1);
}
