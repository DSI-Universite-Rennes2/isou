<?php

/* retourne l'ip du client */
function getIpAddr(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){   //check ip from share internet
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){   //to check ip is pass from proxy
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip=$_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

/* retourne TRUE si l'ip est dans la plage d'ip */
function in_range($ip, $range){
	if(count($range) !== 2){
		if(isset($range[0])){
			$range[1] = $range[0];
		}else{
			return FALSE;
		}
	}

	$range[0] = explode('.', $range[0]);
	$i = count($range[0]);
	if($i < 4){
		for($i;$i<4;$i++){
			$range[0][] = 0;
		}
	}

	$range[1] = explode('.', $range[1]);
	$i = count($range[1]);
	if($i < 4){
		for($i;$i<4;$i++){
			$range[1][] = 255;
		}
	}

	$ip = explode('.',$ip);
	$c = 0;
	foreach($ip as $i){
		if($i < $range[0][$c] || $i > $range[1][$c]){
			return FALSE;
		}
		$c++;
	}
	return TRUE;

}

/* retourne le user-agent du client */
function getInternetBrowser($user_agent){
	/*
		FIREFOX
	*/
	if(preg_match('#Firefox/([\d\.]*)#si',$user_agent, $browser)){
		return str_replace('/', '', $browser[0]);
	}else if(preg_match('#Firefox#si',$user_agent)){
		return 'Firefox';
	/*
		INTERNET EXPLORER
	*/
	}else if(preg_match('#MSIE 8#si',$user_agent)){
		return 'Internet Explorer 8';
	}else if(preg_match('#MSIE 7#si',$user_agent)){
		return 'Internet Explorer 7';
	}else if(preg_match('#MSIE 6#si',$user_agent)){
		return 'Internet Explorer 6';
	}else if(preg_match('#MSIE 5#si',$user_agent)){
		return 'Internet Explorer 5';
	}else if(preg_match('#MSIE 4#si',$user_agent)){
		return 'Internet Explorer 4';
	}else if(preg_match('#MSIE 3#si',$user_agent)){
		return 'Internet Explorer 3';
	}else if(preg_match('#MSIE 2#si',$user_agent)){
		return 'Internet Explorer 2';
	}else if(preg_match('#MSIE 1#si',$user_agent)){
		return 'Internet Explorer 1';
	/*
		OPERA
	*/
	}else if(preg_match('#Opera/8#si',$user_agent)){
		return 'Opera 8';
	}else if(preg_match('#Opera/9#si',$user_agent)){
		return 'Opera 9';
	}else if(preg_match('#Opera/10#si',$user_agent)){
		return 'Opera 10';
	}else if(preg_match('#Opera#si',$user_agent)){
		return 'Opera';
	/*
		CHROME -> should tested before Safari
	*/
	}else if(preg_match('#Chrome/#si',$user_agent)){
		return 'Chrome';
	/*
		ARORA
	*/
	}else if(preg_match('#Arora/#si',$user_agent)){
		return 'Arora';
	/*
		ICEWEASEL
	*/
	}else if(preg_match('#Iceweasel/#si',$user_agent)){
		return 'Iceweasel';
	/*
		EPIPHANY
	*/
	}else if(preg_match('#Epiphany/#si',$user_agent)){
		return 'Epiphany';
	/*
		KONQUEROR
	*/
	}else if(preg_match('#Konqueror/#si',$user_agent)){
		return 'Konqueror';
	/*
		SAFARI
	*/
	}else if(preg_match('#Safari/#si',$user_agent)){
		return 'Safari';
	/*
		LINKS
	*/
	}else if(preg_match('#Links#si',$user_agent)){
		return 'Links';
	/*
		LYNX
	*/
	}else if(preg_match('#Lynx#si',$user_agent)){
		return 'Lynx';
	/*
		DILLO
	*/
	}else if(preg_match('#Dillo/#si',$user_agent)){
		return 'Dillo';
	/*
		PLAYSTATION PORTABLE
	*/
	}else if(preg_match('#PSP#si',$user_agent)){
		return 'PSP';
	}else{
		return 'other';
	}

	/*
		NOT ADDED YET

		* Amaya
		* Avant Browser
		* ELinks
		* Firebird
		* iCab
		* internet explorer mobile
		* Minimo
		* SeaMonkey
		* Netscape Navigator (propriétaire)
		* Netscape (basées sur Mozilla)
		* Off By One
		* OmniWeb
		* w3m
	*/
}


function getOperatingSystem($user_agent){
	/*
		WINDOWS
	*/
	if(preg_match('#Windows NT 6.1#si',$user_agent)){
		return 'Windows 7';
	}elseif(preg_match('#Windows NT 6.0#si',$user_agent)){
		return 'Windows Vista';
	}else if(preg_match('#Windows NT 5.1#si',$user_agent)){
		return 'Windows XP';
	}else if(preg_match('#Windows NT 5.0#si',$user_agent)){
		return 'Windows 2000';
	}else if(preg_match('#Windows 98#si',$user_agent)){
		return 'Windows 98';
	}else if(preg_match('#Windows NT 5.2#si',$user_agent)){
		return 'Windows Server 2003';
	}else if(preg_match('#Windows CE#si',$user_agent)){
		return 'Windows Mobile';
	}else if(preg_match('#Windows#si',$user_agent)){
		return 'Windows';
	/*
		APPLE
	*/
	}else if(preg_match('#iPhone#si',$user_agent)){
		return 'iPhone';
	}else if(preg_match('#iPod#si',$user_agent)){
		return 'iPod';
	}else if(preg_match('#Mac OS X#si',$user_agent)){
		return 'Mac OS X';
	/*
		LINUX
	*/
	}else if(preg_match('#Linux#si',$user_agent)){
		return 'Linux';
	/*
		BSD
	*/
	}else if(preg_match('#BSD#si',$user_agent)){
		return 'BSD';
	/*
		SUN OS
	*/
	}else if(preg_match('#SunOS#si',$user_agent)){
		return 'SunOS';
	/*
		OS/2
	*/
	}else if(preg_match('#OS/2#si',$user_agent)){
		return 'OS/2';
	/*
		PLAYSTATION PORTABLE
	*/
	}else if(preg_match('#PSP#si',$user_agent)){
		return 'PSP';
	/*
		PLAYSTATION 3
	*/
	}else if(preg_match('#PLAYSTATION 3#si',$user_agent)){
		return 'PlayStation 3';
	/*
		NINTENDO WII
	*/
	}else if(preg_match('#Nintendo Wii#si',$user_agent)){
		return 'Nintendo Wii';
	/*
		SYMBIAN OS
	*/
	}else if(preg_match('#SymbianOS#si',$user_agent)){
		return 'SymbianOS';
	/*
		BLACKBERRY
	*/
	}else if(preg_match('#BlackBerry#si',$user_agent)){
		return 'BlackBerry';
	/*
		ANDROID
	*/
	}else if(preg_match('#Android#si',$user_agent)){
		return 'Android';
	/*
		HTC
	*/
	}else if(preg_match('#HTC#si',$user_agent)){
		return 'HTC';
	/*
		SAMSUNG
	*/
	}else if(preg_match('#Samsung#si',$user_agent)){
		return 'Samsung';
	/*
		SONY ERICSSON
	*/
	}else if(preg_match('#SonyEricsson#si',$user_agent)){
		return 'SonyEricsson';
	/*
		OTHERS
	*/
	}else{
		return 'other';
	}
}

?>
