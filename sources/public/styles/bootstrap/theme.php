<?php

$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/css/bootstrap.min.css');
$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/common.css');

if($PAGE_NAME[0] === 'dependances'){
	$STYLES[] = new \Isou\Helpers\Style(URL.'/styles/bootstrap/dependencies.css');
}

