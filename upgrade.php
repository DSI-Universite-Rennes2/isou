<?php

require __DIR__.'/config.php';
require PRIVATE_PATH.'/libs/upgrade.php';

$db_file_path = substr(DB_PATH, 7);
if (is_file($db_file_path) === false) {
    // Installation.
    try {
        initialize_phinx();
    } catch (Exception $exception) {
        echo 'Echec lors de l\'installation !'.PHP_EOL;
        echo $exception->getMessage().PHP_EOL;
        exit(1);
    }

    echo PHP_EOL;
    echo 'Installation terminée.'.PHP_EOL;
    exit(0);
}

require PRIVATE_PATH.'/php/common/database.php';
require PRIVATE_PATH.'/libs/configuration.php';

// Charge la configuration.
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

if (isset($CFG['version']) === false) {
    $CFG['version'] = '';
}

if (CURRENT_VERSION === $CFG['version']) {
    echo 'Aucune mise à jour à effectuer.'.PHP_EOL;
    exit(0);
}

// Avertissement avant la mise à jour.
echo 'Vous vous apprêtez à faire une mise à jour d\'Isou.'.PHP_EOL;
echo 'Avant de lancer la procédure, il est fortement recommandé de faire une sauvegarde du fichier "'.$db_file_path.'"'.PHP_EOL;
echo 'Souhaitez-vous lancer la mise à jour ? (o/n)'.PHP_EOL;
$response = trim(fgets(STDIN));
if (in_array($response, array('o', 'y', 'O', 'Y'), true) === false) {
    exit(0);
}

echo PHP_EOL;

try {
    switch ($CFG['version']) {
        case '':
        case '0.9.0':
            upgrade_090_to_095();
        case '0.9.5':
            upgrade_095_to_096();
        case '0.9.6':
            upgrade_096_to_0100();
        case '0.10.0':
        case '2012-02-16.1':
            upgrade_0100_to_0110();
        case '0.11.0':
        case '2012-03-16.1':
            upgrade_0110_to_100();
        case '1.0.0':
        case '2013-00-00.1':
            upgrade_100_to_200();
    }
} catch (Exception $exception) {
    echo 'Echec lors de la mise à jour !'.PHP_EOL;
    echo $exception->getMessage().PHP_EOL;
    exit(1);
}

echo PHP_EOL;
echo 'Mise à jour terminée.'.PHP_EOL;
