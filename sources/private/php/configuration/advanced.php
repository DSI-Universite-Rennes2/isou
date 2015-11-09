<?php

$TITLE = NAME.' - Configuration avancée';

$_POST['error'] = array();

// vérification du formulaire plage ip locale
if(isset($_POST['iplocalsubmit'])){
	$_POST['error']['iplocal'] = array();
	if(isset($_POST['iplocal1'], $_POST['iplocal2'])){
		// $CFG['ip_local'] = json_decode($CFG['ip_local']);
		if($CFG['ip_local'] === NULL){
			$CFG['ip_local'] = array();
		}

		$_POST['iplocal1'] = trim($_POST['iplocal1']);
		$_POST['iplocal2'] = trim($_POST['iplocal2']);

		if(!empty($_POST['iplocal1'])){
			if(filter_var($_POST['iplocal1'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE){
				$_POST['error']['iplocal']['iplocal1'] = "Le premier champ n'est pas une ipv4 valide";
			}
		}

		if(!empty($_POST['iplocal2'])){
			if(filter_var($_POST['iplocal2'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE){
				$_POST['error']['iplocal']['iplocal2'] = "Le second champ n'est pas une ipv4 valide";
			}
		}

		if(count($_POST['error']['iplocal']) === 0){
			if(!empty($_POST['iplocal1']) && empty($_POST['iplocal2'])){
				$CFG['ip_local'][] = array($_POST['iplocal1']);
			}elseif(!empty($_POST['iplocal2']) && empty($_POST['iplocal1'])){
				$CFG['ip_local'][] = array($_POST['iplocal2']);
			}elseif(empty($_POST['iplocal1']) && empty($_POST['iplocal2'])){
				$_POST['error']['iplocal']['empty_fields'] = 'Au moins une IP doit être saisie';
			}else{
				$ip1 = explode('.',$_POST['iplocal1']);
				$ip2 = explode('.',$_POST['iplocal2']);

				// hmm, peut mieux faire...
				$lesser = FALSE;
				if($ip1[0] >= $ip2[2]){
					$lesser = !($ip1[0] === $ip2[0]);
					if(!$lesser && $ip1[1] >= $ip2[1]){
						$lesser = !($ip1[1] === $ip2[1]);
						if(!$lesser && $ip1[2] >= $ip2[2]){
							$lesser = !($ip1[2] === $ip2[2]);
							if(!$lesser && $ip1[3] >= $ip2[3]){
								$lesser = !($ip1[3] === $ip2[3]);
								if(!$lesser && $ip1[4] >= $ip2[4]){
									$lesser = !($ip1[4] === $ip2[4]);
								}
							}
						}
					}
				}

				if($lesser == TRUE){
					$tmpip = implode('.', $ip2);
					$ip2 = implode('.', $ip1);
					$ip1 = $tmpip;
				}else{
					$ip1 = implode('.', $ip1);
					$ip2 = implode('.', $ip2);
				}

				$CFG['ip_local'][] = array($ip1, $ip2);
			}
		}

		if(count($_POST['error']['iplocal']) === 0){
			echo json_encode($CFG['ip_local']);
			$sql = "UPDATE configuration SET value=? WHERE key='ip_local'";
			$query = $DB->prepare($sql);
			if($query->execute(array(json_encode($CFG['ip_local']))) === FALSE){
				$_POST['error']['iplocal']['error_iplocal'] = 'La clé "ip_local" n\'a pas pu être mise à jour';
			}
		}
	}else{
		$_POST['error']['iplocal']['no_iplocal'] = 'Le formulaire est incomplet';
	}
}


// vérification du formulaire plage ip service
if(isset($_POST['ipservicesubmit'])){
	$_POST['error']['ipservice'] = array();
	if(isset($_POST['ipservice1'], $_POST['ipservice2'])){
		// $CFG['ip_service'] = json_decode($CFG['ip_service']);
		if($CFG['ip_service'] === NULL){
			$CFG['ip_service'] = array();
		}

		$_POST['ipservice1'] = trim($_POST['ipservice1']);
		$_POST['ipservice2'] = trim($_POST['ipservice2']);

		if(!empty($_POST['ipservice1'])){
			if(filter_var($_POST['ipservice1'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE){
				$_POST['error']['ipservice']['ipservice1'] = "Le premier champ n'est pas une ipv4 valide";
			}
		}

		if(!empty($_POST['ipservice2'])){
			if(filter_var($_POST['ipservice2'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE){
				$_POST['error']['ipservice']['ipservice2'] = "Le second champ n'est pas une ipv4 valide";
			}
		}

		if(count($_POST['error']['ipservice']) === 0){
			if(!empty($_POST['ipservice1']) && empty($_POST['ipservice2'])){
				$CFG['ip_service'][] = array($_POST['ipservice1']);
			}elseif(!empty($_POST['ipservice2']) && empty($_POST['ipservice1'])){
				$CFG['ip_service'][] = array($_POST['ipservice2']);
			}elseif(empty($_POST['ipservice1']) && empty($_POST['ipservice2'])){
				$_POST['error']['ipservice']['empty_fields'] = 'Au moins une IP doit être saisie';
			}else{
				$ip1 = explode('.',$_POST['ipservice1']);
				$ip2 = explode('.',$_POST['ipservice2']);

				// hmm, peut mieux faire...
				$lesser = FALSE;
				if($ip1[0] >= $ip2[2]){
					$lesser = !($ip1[0] === $ip2[0]);
					if(!$lesser && $ip1[1] >= $ip2[1]){
						$lesser = !($ip1[1] === $ip2[1]);
						if(!$lesser && $ip1[2] >= $ip2[2]){
							$lesser = !($ip1[2] === $ip2[2]);
							if(!$lesser && $ip1[3] >= $ip2[3]){
								$lesser = !($ip1[3] === $ip2[3]);
								if(!$lesser && $ip1[4] >= $ip2[4]){
									$lesser = !($ip1[4] === $ip2[4]);
								}
							}
						}
					}
				}

				if($lesser == TRUE){
					$tmpip = implode('.', $ip2);
					$ip2 = implode('.', $ip1);
					$ip1 = $tmpip;
				}else{
					$ip1 = implode('.', $ip1);
					$ip2 = implode('.', $ip2);
				}

				$CFG['ip_service'][] = array($ip1, $ip2);
			}
		}

		if(count($_POST['error']['ipservice']) === 0){
			$sql = "UPDATE configuration SET value=? WHERE key='ip_service'";
			$query = $DB->prepare($sql);
			if($query->execute(array(json_encode($CFG['ip_service']))) === FALSE){
				$_POST['error']['ipservice']['error_ipservice'] = 'La clé "ip_service" n\'a pas pu être mise à jour';
			}
		}
	}else{
		$_POST['error']['ipservice']['no_ipservice'] = 'Le formulaire est incomplet';
	}
}

// vérification du formulaire d'ajout d'administrateur
if(isset($_POST['adminusers'])){
	$_POST['error']['adminusers'] = array();
	$_POST['adminusers'] = trim($_POST['adminusers']);
	if(empty($_POST['adminusers'])){
		$_POST['error']['adminusers']['empty_field'] = 'Le champ ne peut pas être vide.';
	}else{
		if($CFG['admin_users'] === NULL){
			$CFG['admin_users'] = array();
		}
		$CFG['admin_users'][] = $_POST['adminusers'];

		$sql = "UPDATE configuration SET value=? WHERE key='admin_users'";
		$query = $DB->prepare($sql);
		if($query->execute(array(json_encode($CFG['admin_users']))) === FALSE){
			$_POST['error']['adminusers']['error_adminusers'] = 'La clé "admin_users" n\'a pas pu être mise à jour';
		}else{
			unset($_POST['adminusers']);
		}
	}
}

// vérification du formulaire d'ajout d'un mail d'administrateur
if(isset($_POST['adminmails'])){
	$_POST['error']['adminmails'] = array();
	$_POST['adminmails'] = trim($_POST['adminmails']);
	if(empty($_POST['adminmails'])){
		$_POST['error']['adminmails']['empty_field'] = 'Le champ ne peut pas être vide.';
	}else{
		if($CFG['admin_mails'] === NULL){
			$CFG['admin_mails'] = array();
		}
		$CFG['admin_mails'][] = $_POST['adminmails'];

		$sql = "UPDATE configuration SET value=? WHERE key='admin_mails'";
		$query = $DB->prepare($sql);
		if($query->execute(array(json_encode($CFG['admin_mails']))) === FALSE){
			$_POST['error']['adminmails']['error_adminmails'] = 'La clé "admin_mails" n\'a pas pu être mise à jour';
		}else{
			unset($_POST['adminmails']);
		}
	}
}

// vérification du formulaire d'ajout de l'expéditeur
if(isset($_POST['localmail'])){
	$_POST['error']['localmail'] = array();
	$_POST['localmail'] = trim($_POST['localmail']);
	if(!empty($_POST['localmail']) && !filter_var($_POST['localmail'], FILTER_VALIDATE_EMAIL)){
		$_POST['error']['localmail']['error_localmail'] = 'L\'adresse mail saisie n\'est pas valide';
	}else{
		$sql = "UPDATE configuration SET value=? WHERE key='local_mail'";
		$query = $DB->prepare($sql);
		if($query->execute(array($_POST['localmail'])) === FALSE){
			$_POST['error']['localmail']['error_localmail'] = 'La clé "local_mail" n\'a pas pu être mise à jour';
		}
	}
}

// vérification du formulaire d'ajout de la clé auto_backup
$smarty->assign('autobackup', array('Non', 'Oui'));
if(isset($_POST['autobackup'])){
	$_POST['error']['autobackup'] = array();
	$_POST['autobackup'] = intval($_POST['autobackup']);
	if($_POST['autobackup'] !== 1){
		$_POST['autobackup'] = 0;
	}
	$sql = "UPDATE configuration SET value=? WHERE key=?";
	$query = $DB->prepare($sql);
	if($query->execute(array($_POST['autobackup'], 'auto_backup')) === FALSE){
		$_POST['error']['autobackup']['error_autobackup'] = 'La clé "auto_backup" n\'a pas pu être mise à jour';
	}
}


// traitement des liens "d'effacement"
if(isset($_GET['action'], $_GET['key'])){
	// reset cron
	if($_GET['action'] === 'reset'){
		if(in_array($_GET['key'], array('last_cron_update', 'last_daily_cron_update', 'last_weekly_cron_update', 'last_yearly_cron_update')) === TRUE){
			$_POST['error'][$_GET['key']] = array();
			$sql = "UPDATE configuration SET value=NULL WHERE key=?";
			$query = $DB->prepare($sql);
			if($query->execute(array($_GET['key'])) === FALSE){
				$_POST['error'][$_GET['key']]['error_db'] = 'La clé "'.$_GET['key'].'" n\'a pas pu être mise à jour';
			}else{
				$CFG[$_GET['key']] = 0;
				$_POST['error'][$_GET['key']]['none'] = 'La clé "'.$_GET['key'].'" a été mise à jour';
			}
		}	
	}elseif($_GET['action'] === 'drop' && isset($_GET['index'])){
		if(in_array($_GET['key'], array('ip_local', 'ip_service', 'admin_users', 'admin_mails')) === TRUE){
			$_POST['error'][$_GET['key']] = array();
			if(isset($CFG[$_GET['key']][$_GET['index']-1])){
				unset($CFG[$_GET['key']][$_GET['index']-1]);
				// re-order index keys
				$CFG[$_GET['key']] = array_merge($CFG[$_GET['key']]);
				$sql = "UPDATE configuration SET value=? WHERE key=?";
				$query = $DB->prepare($sql);
				if($query->execute(array(json_encode($CFG[$_GET['key']]), $_GET['key'])) === FALSE){
					$_POST['error'][$_GET['key']]['error_db'] = 'La valeur n\'a pas pu être supprimée de la clé "'.$_GET['key'].'".';
				}else{
					$_POST['error'][$_GET['key']]['none'] = 'La valeur a été supprimée de la clé "'.$_GET['key'].'".';
				}
			}else{
				$_POST['error'][$_GET['key']]['bad_index'] = "L'index indiqué n'est pas valide";
			}
		}
	}
}

$smarty->assign('CFG', $CFG);
$smarty->assign('error', $_POST['error']);

?>
