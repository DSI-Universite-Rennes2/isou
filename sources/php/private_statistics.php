<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/stats.css" media="screen">
			<link rel="stylesheet" type="text/css" href="'.URL.'/css/common.css" media="print">
			<link rel="stylesheet" type="text/css" href="'.URL.'/css/stats_print.css" media="print">';
	$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>';
	$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_statistics.js"></script>';
	$title = NAME.' - Administration des Statistiques';


	if(isset($_GET['history'])){
		require BASE.'/php/private_statistics_history.php';
	}else if(isset($_GET['visits'])){
		$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/stats.css" media="screen">
				<link rel="stylesheet" type="text/css" href="'.URL.'/css/common.css" media="print">
				<link rel="stylesheet" type="text/css" href="'.URL.'/css/stats_print.css" media="print">
				<link type="text/css" rel="stylesheet" href="'.URL.'/css/visualize.jQuery.css"/>
				<link type="text/css" rel="stylesheet" href="'.URL.'/css/stats_isou.css"/>';
		$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/js/jquery.visualize.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_statistics_isou.js"></script>';
		require BASE.'/php/private_statistics_isou.php';
	}else{
		$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/stats.css" media="screen">
				<link rel="stylesheet" type="text/css" href="'.URL.'/css/common.css" media="print">
				<link rel="stylesheet" type="text/css" href="'.URL.'/css/stats_print.css" media="print">
				<link type="text/css" rel="stylesheet" href="'.URL.'/css/visualize.jQuery.css"/>
				<link type="text/css" rel="stylesheet" href="'.URL.'/css/stats_graphic.css"/>';
		$script = '<script type="text/javascript" src="'.URL.'/js/jquery-1.3.2.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/js/jquery.visualize.js"></script>';
		$script .= '<script type="text/javascript" src="'.URL.'/js/jquery_statistics_graphic.js"></script>';
		require BASE.'/php/private_statistics_graphic.php';
	}

?>
