<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/services.css" media="screen" />';
	$title = NAME.' - Administration des Services ISOU et NAGIOS';

	$update = '';

	require BASE.'/php/private_services.formsprocess.php';

	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-min.js"></script>';
	if(isset($_GET['service']) && $_GET['service'] == 'nagios'){
		require BASE.'/php/private_services_nagios.php';
		$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_services_nagios.js"></script>';
	}else{
		require BASE.'/php/private_services_isou.php';
		$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_services_isou.js"></script>';
	}

?>
