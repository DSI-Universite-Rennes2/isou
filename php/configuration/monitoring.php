<?php

$TITLE = NAME.' - Configuration de logiciels de monitoring utilisés en arrière-plan d\'isou';

$options_yes_no = array(1 => 'yes', 0 => 'no');

$nagios_statusdat = array('nagios_statusdat_enable', 'nagios_statusdat_path');
$shinken_thruk = array('shinken_thruk_enable', 'shinken_thruk_path', 'shinken_thruk_username', 'shinken_thruk_password');

$backends = array_merge($nagios_statusdat, $shinken_thruk);

foreach($backends as $key){
	if(isset($_POST[$key]) && $_POST[$key] !== $CFG[$key]){
		$value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
		if(set_configuration($key, $value)){
			$CFG[$key] = $value;
		}
	}
}

$smarty->assign('options_yes_no', $options_yes_no);

$SUBTEMPLATE = 'configuration/monitoring.tpl';

