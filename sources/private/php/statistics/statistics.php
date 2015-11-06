<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/stats.css" media="screen">
			<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/common.css" media="print">
			<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/stats_print.css" media="print">';
	$script = '<script type="text/javascript" src="'.URL.'/scripts/jquery-min.js"></script>';
	$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_statistics.js"></script>';
	$title = NAME.' - Administration des Statistiques';


	if(isset($_GET['history'])){
		require BASE.'/php/statistics/history.php';
	}else if(isset($_GET['visits'])){
		$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/stats.css" media="screen">
				<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/common.css" media="print">
				<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/stats_print.css" media="print">
				<link type="text/css" rel="stylesheet" href="'.URL.'/styles/classic/visualize.jQuery.css"/>
				<link type="text/css" rel="stylesheet" href="'.URL.'/styles/classic/stats_isou.css"/>';
		$script = '<script type="text/javascript" src="'.URL.'/scripts/jquery-min.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery.visualize.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_statistics_isou.js"></script>';
		require BASE.'/php/statistics/isou.php';
	}else{
		$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/stats.css" media="screen">
				<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/common.css" media="print">
				<link rel="stylesheet" type="text/css" href="'.URL.'/styles/classic/stats_print.css" media="print">
				<link type="text/css" rel="stylesheet" href="'.URL.'/styles/classic/visualize.jQuery.css"/>
				<link type="text/css" rel="stylesheet" href="'.URL.'/styles/classic/stats_graphic.css"/>';
		$script = '<script type="text/javascript" src="'.URL.'/scripts/jquery-min.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery.visualize.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/scripts/jquery_statistics_graphic.js"></script>';
		require BASE.'/php/statistics/graphic.php';
	}

?>
