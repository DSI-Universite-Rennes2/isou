<?php

require_once PRIVATE_PATH.'/libs/services.php';

$smarty->assign('services', get_services(UniversiteRennes2\Isou\Service::TYPE_ISOU));

$TEMPLATE = 'dependencies/services/list.tpl';

?>
