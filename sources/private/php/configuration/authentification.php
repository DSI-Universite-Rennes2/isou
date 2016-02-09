<?php

$TITLE = NAME.' - Configuration de l\'authentification';

$cas_admin_usernames = implode(PHP_EOL, $CFG['authentification_cas_admin_usernames']);

$options_yes_no = array(1 => 'yes', 0 => 'no');

$auth_cas = array('authentification_cas_enabled', 'authentification_cas_admin_usernames');
$auth_manual = array('authentification_manual_enabled', 'authentification_manual_path', 'authentification_manual_password');

$auths = array_merge($auth_cas, $auth_manual);

foreach($auths as $key){
	if(isset($_POST[$key]) && $_POST[$key] !== $CFG[$key]){
		if($key === 'authentification_cas_admin_usernames'){
			$CFG['authentification_cas_admin_usernames'] = array();
			foreach(explode(PHP_EOL, $_POST[$key]) as $user){
				$user = trim($user);
				if(!empty($user)){
					$CFG['authentification_cas_admin_usernames'][$user] = htmlentities($user, ENT_QUOTES, 'UTF-8');
				}
			}

			$CFG['authentification_cas_admin_usernames'] = array_values($CFG['authentification_cas_admin_usernames']);

			$value = json_encode($CFG['authentification_cas_admin_usernames']);

			$cas_admin_usernames = implode(PHP_EOL, $CFG['authentification_cas_admin_usernames']);
		}else{
			$value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
		}

		if(set_configuration($key, $value)){
			$CFG[$key] = $value;
		}
	}
}

$smarty->assign('options_yes_no', $options_yes_no);
$smarty->assign('cas_admin_usernames', $cas_admin_usernames);


$SUBTEMPLATE = 'configuration/authentification.tpl';

