<?php

function get_configurations() {
    global $DB;

    $json_attributes = array('authentification_cas_admin_usernames', 'notification_receivers');
    $datetime_attributes = array('last_check_update', 'last_cron_update', 'last_daily_cron_update', 'last_update', 'last_weekly_cron_update', 'last_yearly_cron_update');

    $sql = "SELECT key, value FROM configuration";
    $configurations = array();
    if ($query = $DB->query($sql)) {
        while ($config = $query->fetch(PDO::FETCH_OBJ)) {
            if (in_array($config->key, $json_attributes, true) === true) {
                $configurations[$config->key] = json_decode($config->value);
            } elseif (in_array($config->key, $datetime_attributes, true) === true) {
                try {
                    $configurations[$config->key] = new DateTime($config->value);
                } catch (Exception $exception) {
                    $configurations[$config->key] = new DateTime();
                }
            } else {
                $configurations[$config->key] = $config->value;
            }
        }
    }

    return $configurations;
}

function set_configuration($key, $value, $field=NULL){
	global $DB;

	$sql = "UPDATE configuration SET value=? WHERE key=?";
	$query = $DB->prepare($sql);
	if($query->execute(array($value, $key))){
		if($field === NULL){
			$_POST['successes'][] = 'Mise à jour de la clé "'.$key.'".';
		}else{
			$_POST['successes'][] = 'Mise à jour du champ "'.$field.'".';
		}

		return true;
	}else{
		if($field === NULL){
			$_POST['errors'][] = 'Erreur lors de la mise à jour de la clé "'.$key.'".';
		}else{
			$_POST['errors'][] = 'Erreur lors de la mise à jour du champ "'.$field.'".';
		}

		return false;
	}
}
