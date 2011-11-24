<?php

error_reporting(0);

if(DEBUG === TRUE){
	$IS_ADMIN = true;
	$IS_AUTH = false;
}else{
	require BASE.'/classes/phpCAS/CAS.php';

	// initialize phpCAS
	phpCAS::client(CAS_VERSION_2_0, CAS_URL, CAS_PORT, CAS_URI);

	// no SSL validation for the CAS server
	phpCAS::setNoCasServerValidation();

	if(isset($_GET['connexion'])){
		phpCAS::forceAuthentication();
	}

	if(isset($_GET['deconnexion'])){
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		phpCAS::logoutWithRedirectService(ISOU_URL);
		exit(0);
	}

	$IS_AUTH = phpCAS::isAuthenticated();
	$IS_ADMIN = false;
}

if(DEV === TRUE || DEBUG === TRUE){
	error_reporting(-1);
}else{
	error_reporting(0);
}

if($IS_AUTH === true){
	if(in_array(phpCAS::getUser(), $CFG['admin_users']) === TRUE){
		$IS_ADMIN = true;
	}
}

?>
