<?php

	$TITLE = NAME.' - Administration des Services ISOU et NAGIOS';

	$update = '';

	require PRIVATE_PATH.'/php/services/formsprocess.php';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');

	if(isset($_GET['service']) && $_GET['service'] == 'nagios'){
		require PRIVATE_PATH.'/php/services/nagios.php';
		$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_services_nagios.js');
	}else{
		require PRIVATE_PATH.'/php/services/isou.php';
		$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_services_isou.js');
	}

	$template = 'services/services.tpl';

?>
