<?php

	$TITLE = NAME.' - Administration des Statistiques';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_statistics.js');

	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/stats.css');
	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/common.css', 'print');
	$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/stats_print.css', 'print');

	if(isset($_GET['history'])){
		require PRIVATE_PATH.'/php/statistics/history.php';
	}else{
		$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery.visualize.js');
		$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_statistics_graphic.js');

		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/visualize.jQuery.css');
		$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/stats_graphic.css');

		require PRIVATE_PATH.'/php/statistics/graphic.php';
	}

?>
