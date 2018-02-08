<?php

use UniversiteRennes2\Isou\Service;

require_once PRIVATE_PATH.'/libs/services.php';

$smarty->assign('services', get_services(array('type' => Service::TYPE_ISOU)));

$TEMPLATE = 'dependencies/services/list.tpl';

?>
