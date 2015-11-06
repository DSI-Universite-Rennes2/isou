<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/services.css" media="screen" />';
	$title = NAME.' - Administration des Services ISOU et NAGIOS';

	$update = '';

	require BASE.'/php/services/formsprocess.php';

	$script = '<script type="text/javascript" src="'.URL.'/scripts/jquery-min.js"></script>';
	if(isset($_GET['service']) && $_GET['service'] == 'nagios'){
		require BASE.'/php/services/nagios.php';
		$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_services_nagios.js"></script>';
	}else{
		require BASE.'/php/services/isou.php';
		$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_services_isou.js"></script>';
	}

?>
