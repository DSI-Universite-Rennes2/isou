<?php

	$TITLE = NAME.' - Administration des Statistiques';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_statistics.js');

	if(isset($_GET['history'])){
		require PRIVATE_PATH.'/php/statistics/history.php';
	}else{
		$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery.visualize.js');
		$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_statistics_graphic.js');

		require PRIVATE_PATH.'/php/statistics/graphic.php';
	}

	$template = 'statistics/statistics.tpl';

?>
