<?php

// retourne le nom de la page
// ex : 
//		url appelé : http://cursus.uhb.fr/index.php/inscriptions?var=1
//		nom retourné par la fonction : inscriptions
function get_page_name($script_called, $drop_script_name = false){
	$uri = $_SERVER["REQUEST_URI"];
	
	$pos = strpos($uri, '/'.$script_called);
	
	if($var = strpos($uri, '?')){
		$uri = substr($uri, 0, $var);
	}
	
	if($drop_script_name){
		return substr($uri, (strlen(' /'.$script_called)+$pos));
	}else{
		return substr($uri, $pos);
	}
}

// retourne la base de l'url, sans le nom du script appelé
// ex : 
//		url appelé : http://cursus.uhb.fr/methodo/index.php?page=2
//		url retourné par la fonction : http://cursus.uhb.fr/methodo/
function get_base_url($type = 'dirname', $secured = false){
	($secured)?$secured = 'https://':$secured = 'http://';

	if(!isset($_SERVER['SERVER_NAME'])){
		$_SERVER['SERVER_NAME'] = '.';
	}

	if(isset($_SERVER["HTTP_X_FORWARDED_HOST"])){
		$_SERVER['SERVER_NAME'] = $_SERVER["HTTP_X_FORWARDED_HOST"];
	}

	if(isset($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], array(80, 443))){
		$_SERVER['SERVER_NAME'] .= ':'.$_SERVER['SERVER_PORT'];
	}

	if($type == 'dirname'){
		// exemple : http://moodledev-1.uhb.fr/isou/
		return $secured.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);	
	}else if($type == 'basename'){
		// exemple : http://moodledev-1.uhb.fr/isou/phpinfo.php
		return $secured.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
	}else if($type == 'noget'){
		// exemple : http://moodledev-1.uhb.fr/isou/phpinfo.php/toto
		return $secured.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	}else{
		// exemple : http://moodledev-1.uhb.fr/isou/phpinfo.php/toto?login
		if(strpos($_SERVER['REQUEST_URI'],$_SERVER['SCRIPT_NAME']) === false){
			return $secured.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
		}else{
			return $secured.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		}		
	}
}

/*
 * retourne une chaine sans ses accents et sans parantheses, dieses, etc...
 * ex : déçu -> decu
 */
function strip_accents($str, $utf8 = true) {
	$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ \']@i','@[^a-zA-Z0-9_]@');
	$replace = array ('e','a','i','u','o','c','_','');
	
	if($utf8){
		$str = utf8_decode($str);
		$search = array_map("utf8_decode", $search);
	}
	
	return preg_replace($search, $replace, $str);
	// return strtr($str,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}
