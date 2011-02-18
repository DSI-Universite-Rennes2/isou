<?php

$pwd = dirname(__FILE__).'/..';

require $pwd.'/functions.php';
require $pwd.'/config.php';
require BASE.'/classes/isou/update.functions.php';
require BASE.'/classes/isou/parser.function.php';

// creation/modification du fichier de log
$log = update_nagios_to_db();

if(is_a($log,'Exception')){
	add_log(LOG_FILE, 'ISOU', 'error', $log->getMessage());
}

require BASE.'/classes/isou/json.php';

?>
