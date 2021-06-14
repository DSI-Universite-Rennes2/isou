<?php
/**
 * This file is part of isou project.
 *
 * Fonctions liées aux procédures de mise à jour.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use UniversiteRennes2\Isou\Plugin;

/**
 * Met à jour la date de dernière mise à jour et le numéro de version d'isou.
 *
 * @return void
 */
function isou_update_version() {
    global $DB;

    echo 'Votre version d\'isou est '.CURRENT_VERSION.'.'.PHP_EOL;

    $update = array();
    $update['last_update'] = strftime('%FT%T');
    $update['version'] = CURRENT_VERSION;

    foreach ($update as $key => $value) {
        $sql = "UPDATE configuration SET value = :value WHERE key = :key";
        $query = $DB->prepare($sql);
        $query->execute(array(':value' => $value, ':key' => $key));
    }
}

/**
 * Procède à la migration de la version 1.0.0 (2013-00-00.1) à la version 2.0.0.
 *
 * @throws Exception Lève une exception lorsqu'une erreur survient.
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

    // Fusion les bases de données annualisées.
    try {
        $DB = new PDO(DB_PATH, '', '');
        $DB->beginTransaction();
    } catch (PDOException $exception) {
        throw new Exception($exception->getMessage());
    }

    $old_databases = array();

    $db_path = dirname(substr(DB_PATH, strlen('sqlite:')));

    $handle = opendir($db_path);
    if ($handle !== false) {
        while (($entry = readdir($handle)) !== false) {
            if ($entry[0] === '.') {
                continue;
            }

            if (is_file($db_path.'/'.$entry) === false) {
                continue;
            }

            if (preg_match('/^isou-[0-9]+\.sqlite3$/', $entry) !== 1) {
                continue;
            }

            $old_databases[] = $entry;
        }

        closedir($handle);
    }

    sort($old_databases);
    if (count($old_databases) !== 0) {
        echo PHP_EOL;
        echo ' **  Fusionne les anciennes bases de données annualisées...'.PHP_EOL;
    }

    foreach ($old_databases as $entry) {
        echo ' ==   - Fusionne la base de données '.$entry.' : ';

        try {
            $old_db = new PDO('sqlite:'.$db_path.'/'.$entry, '', '');
        } catch (PDOException $exception) {
            echo $exception->getMessage().PHP_EOL;
            continue;
        }

        $sql = 'SELECT e.beginDate, e.endDate, ei.period, ei.isScheduled, ei.idService, ed.description'.
            ' FROM events e'.
            ' JOIN events_isou ei ON e.idEvent = ei.idEvent'.
            ' JOIN events_description ed ON ed.idEventDescription = ei.idEventDescription'.
            ' WHERE e.typeEvent = 0'.
            ' AND ei.isScheduled < 2'; // On récupère uniquement l'historique des interruptions prévues et imprévues.
        $query = $old_db->prepare($sql);
        if ($query === false) {
            echo 'Impossible de parcourir la base de données '.$entry.'. Le schéma de données n\'est pas correct.'.PHP_EOL;
            continue;
        }
        $query->execute();
        $old_events = $query->fetchAll(PDO::FETCH_OBJ);

        $count = 0;
        $total = count($old_events);

        foreach ($old_events as $old_event) {
            $sql = "SELECT id, description, autogen FROM events_descriptions WHERE description = :description";
            $query = $DB->prepare($sql);
            $query->execute(array(':description' => $old_event->description));
            $description = $query->fetch(PDO::FETCH_OBJ);

            if ($description === false) {
                $sql = "INSERT INTO events_descriptions(description, autogen) VALUES (:description, :autogen)";
                $query = $DB->prepare($sql);
                $query->execute(array(':description' => $old_event->description, ':autogen' => $old_event->autogen));

                $description = new stdClass();
                $description->id = $DB->lastInsertId();
            }

            try {
                $startdate = new DateTime($old_event->beginDate);
                $startdate = $startdate->format('Y-m-d\TH:i:s');
            } catch (Exception $exception) {
                continue;
            }

            if (empty($old_event->endDate) === true) {
                $enddate = null;
            } else {
                try {
                    $enddate = new DateTime($old_event->endDate);
                    $enddate = $enddate->format('Y-m-d\TH:i:s');
                } catch (Exception $exception) {
                    continue;
                }
            }

            $params = array();
            $params[':startdate'] = $startdate;
            $params[':enddate'] = $enddate;
            $params[':state'] = 2;
            $params[':type'] = $old_event->isScheduled;
            $params[':period'] = null;
            $params[':ideventdescription'] = $description->id;
            $params[':idservice'] = $old_event->idService;

            $sql = "INSERT INTO events(startdate, enddate, state, type, period, ideventdescription, idservice)".
                " VALUES(:startdate, :enddate, :state, :type, :period, :ideventdescription, :idservice)";
            $query = $DB->prepare($sql);
            $query->execute($params);

            $count++;
        }

        echo $count.'/'.$total.' évènements fusionnés.'.PHP_EOL;
    }

    $DB->commit();
}

/**
 * Procède à la migration de la version 0.11.0 (2012-03-16.1) à la version 1.0.0 (2013-00-00.1).
 *
 * @throws Exception Lève une exception lorsqu'une erreur survient.
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
 * @throws Exception Lève une exception lorsqu'une erreur survient.
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
 * @throws Exception Lève une exception lorsqu'une erreur survient.
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
 * @throws Exception Lève une exception lorsqu'une erreur survient.
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
 * @throws Exception Lève une exception lorsqu'une erreur survient.
 *
 * @return void
 */
function upgrade_090_to_095() {
    echo 'Mise à jour de la version 0.9.0 vers la version 0.9.5.'.PHP_EOL;

    throw new Exception('Not implemented. TODO...');
}

/**
 * Détermine si un plugin doit être mis à jour ou applique les mises à jour.
 *
 * @param boolean $check_only Indique si les mises à jour doivent être signalées ou appliquées.
 *
 * @return boolean Retourne true si une mise à jour est disponible ou si les mises à jour ont été faites correctement.
 */
function upgrade_plugins(bool $check_only = false) {
    global $DB, $LOGGER;

    $plugins_paths = array();
    $plugins_paths['authentification'] = PRIVATE_PATH.'/plugins/authentification';
    $plugins_paths['monitoring'] = PRIVATE_PATH.'/plugins/monitoring';
    $plugins_paths['view'] = PRIVATE_PATH.'/plugins/view';

    foreach ($plugins_paths as $plugintype => $plugins_path) {
        $entries = array();

        $handle = opendir($plugins_path);
        if ($handle !== false) {
            while (($entry = readdir($handle)) !== false) {
                if ($entry[0] === '.') {
                    continue;
                }

                if (is_dir($plugins_path.'/'.$entry) === false) {
                    continue;
                }

                if (is_file($plugins_path.'/'.$entry.'/version.php') === false) {
                    $LOGGER->addInfo('Le fichier '.$entry.'/version.php n\'existe pas.');
                    continue;
                }

                $entries[] = $entry;
            }

            closedir($handle);
        }

        foreach ($entries as $entry) {
            $module = include($plugins_path.'/'.$entry.'/version.php');

            if (is_object($module) === false) {
                $LOGGER->addInfo('Le fichier '.$entry.'/version.php ne retourne pas un objet.');
                continue;
            }

            $module->codename = $entry;
            $plugin = Plugin::get_record(array('codename' => $module->codename));

            if ($plugin === false) {
                // Install new plugin.
                if ($check_only === true) {
                    return true;
                }

                $plugin = new Plugin();
                $plugin->name = $module->name;
                $plugin->codename = $module->codename;
                $plugin->type = $plugintype;
                $plugin->version = $module->version;

                $plugin->install();

                $plugin->settings = $module->settings;
                $plugin->install_settings();

                continue;
            }

            if ($plugin->version !== $module->version) {
                // Update plugin.
                if ($check_only === true) {
                    return true;
                }

                $plugin->name = $module->name;
                $plugin->version = $module->version;

                $plugin->update();

                $plugin->settings = $module->settings;
                $plugin->update_settings($overwrite = false);
            }
        }
    }

    if ($check_only === true) {
        // Si on arrive ici, c'est qu'il n'y a pas de mises à jour à faire.
        return false;
    } else {
        // Si on arrive ici, c'est que l'application des mises à jour s'est bien passée.
        return true;
    }
}

/**
 * Initialise la création de la base de données.
 *
 * @throws Exception Lève une exception lorsqu'une erreur survient.
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
