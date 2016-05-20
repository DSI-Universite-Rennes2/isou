<?php

// Retourne true si l'adresse mail est valide
function is_valid_email($email) {
  	return preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $email);
	// alt : return preg_match('#^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$#si',$email);
}

// Detruit une session
// return void()
function destroy(){
	// Détruit toutes les variables de session
	$_SESSION = array();

	// Si vous voulez détruire complètement la session, effacez également
	// le cookie de session.
	// Note : cela détruira la session et pas seulement les données de session !
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	// Finalement, on détruit la session.
	session_destroy();	
}

// Verifie si l'application est ouverte
// return boolean
function is_opened($APP_OPENED){
	
	$closed = true;
	$i = 0;
	while(isset($APP_OPENED[$i][0]) && $closed){
		if(TIME > $APP_OPENED[$i][0] && TIME < $APP_OPENED[$i][1]){
			$closed = false;
		}else{
			$i++;
		}
	}
	
	return !$closed;
}

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

// prend un formulaire <select> en paramètre 
// et recherche la selection courante
function form_selected($select, $id){
	return str_replace('<option value="'.$id.'">','<option value="'.$id.'" selected="selected">',$select);
}


// converti une date JJ/MM/AA en timestamp
// ou
// converti une date JJ/MM/YYYY HH:MM en timestamp
function date_to_timestamp($date){
	if(strlen($date) == 16){
		$pattern = '#\d{2}/\d{2}/\d{4} \d{2}:\d{2}#';
		$date_second = 0;
		$date_month = intval(substr($date,0,2));
		$date_day = intval(substr($date,3,2));
		$date_year = intval(substr($date,6,4));
		$date_hour = intval(substr($date,11,2));
		$date_minute = intval(substr($date,14,2));
	}else if(strlen($date) == 8){
		$pattern = '#\d{2}/\d{2}/\d{2}#';
		$date_day = substr($date,0,2);
		$date_month = substr($date,3,2);
		$date_year = substr($date,6,2);	
		$date_second = 0;	
		$date_hour = 0;
		$date_minute = 0;
	}else{
		return false;
	}

	if(preg_match($pattern,$date)){
		return mktime($date_hour,$date_minute,$date_second,$date_month,$date_day,$date_year);
	}else{
		return false;
	}
}

function plurial($num, $word, $whitespace = TRUE, $lang = 'fr'){
	if($lang === 'fr'){
		if($whitespace === TRUE){
			return ($num>1)?$word .= 's':$word .= '&nbsp';
		}else{
			return ($num>1)?$word .= 's':$word;
		}
	}
	return $word;
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

/*
   Convert seconds to human readable
*/
function secondstohuman($seconds){
	$minutes = floor($seconds/60);
	if($minutes >= 1){
		$hours = floor($seconds/3600);
		if($hours >= 1){
			$days = floor($seconds/86400);
			if($days >= 1){
				return $days.' '.plurial($days, 'jour', FALSE);
			}else{
				return $hours.' '.plurial($hours, 'heure', FALSE);
			}
		}else{
			return $minutes.' '.plurial($minutes, 'minute', FALSE);
		}
	}else{
		return $seconds.' '.plurial($seconds, 'seconde', FALSE);
	}
}

function isoumail($to, $subject, $message){
	global $CFG;

	if(isset($CFG['local_mail']) && filter_var($CFG['local_mail'], FILTER_VALIDATE_EMAIL)){
		$from = $CFG['local_mail'];
	}else{
		$from = $to;
	}

	$headers =	"From: Message automatique de ".NAME." <".$from.">\r\n".
				"Reply-To: ".$from."\r\n".
				"MIME-Version: 1.0\r\n".
				"Content-type: text/plain; charset=UTF-8\r\n";
	$additionnal = "-f $from";

	return mail($to, mb_encode_mimeheader($subject), $message, $headers, $additionnal);
}

?>
