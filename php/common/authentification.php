<?php

if (DEV === true) {
	$IS_ADMIN = true;
	$IS_AUTH = false;
} else {
	// initialize phpCAS
	phpCAS::client(CAS_VERSION_2_0, CAS_URL, CAS_PORT, CAS_URI);

	// no SSL validation for the CAS server
	phpCAS::setNoCasServerValidation();

	if (isset($_GET['connexion']) === true) {
		phpCAS::forceAuthentication();
	}

	if (isset($_GET['deconnexion']) === true) {
		$_SESSION = array();
		if (isset($_COOKIE[session_name()]) === true) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		phpCAS::logoutWithRedirectService(ISOU_URL);
		exit(0);
	}

	$IS_AUTH = phpCAS::isAuthenticated();
	$IS_ADMIN = false;
}

if ($IS_AUTH === true && DEV === false) {
	if(in_array(phpCAS::getUser(), $CFG['admin_users'], true) === true){
		$IS_ADMIN = true;
	}
}
