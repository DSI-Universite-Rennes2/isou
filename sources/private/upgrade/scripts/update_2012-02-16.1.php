<?php

if(defined('STDIN')){
	echo "\033[0;35m\nNouvelles variables :\033[0m\n";

	$error = TRUE;
	while($error === TRUE){
		echo "\nSouhaitez-vous avec un expéditeur spécifique pour les alertes mails de l'application ?\n";
		echo "Défaut: \033[1;34mvide\033[0m\n";
		echo "exemple : \033[1;30misou@example.com\033[0m\n";
		$LOCAL_MAIL = trim(fgets(STDIN));

		if(!empty($LOCAL_MAIL) && !filter_var($LOCAL_MAIL, FILTER_VALIDATE_EMAIL)){
			echo "\n\033[0;31mL'adresse mail saisie n'est pas valide.\033[0m\n";
		}else{
			$error = FALSE;
		}
	}

	$sql = "UPDATE configuration SET value=? WHERE key=?";
	$query = $db->prepare($sql);
	$display = 'Insertion de la clé "local_mail" dans la table configuration';
	if($query->execute(array($LOCAL_MAIL, 'local_mail')) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

	echo "\nSouhaitez-vous activer les sauvegardes automatiques lors des mises à jour de l'application ?\n";
	echo "Défaut: \033[1;34mOui\033[0m\n";
	$AUTO_BACKUP = trim(fgets(STDIN));

	if($AUTO_BACKUP == 0){
		$AUTO_BACKUP = 0;
	}else{
		$AUTO_BACKUP = 1;
	}

	$sql = "UPDATE configuration SET value=? WHERE key=?";
	$query = $db->prepare($sql);
	$display = 'Insertion de la clé "auto_backup" dans la table configuration';
	if($query->execute(array($AUTO_BACKUP, 'auto_backup')) === FALSE){
		echo $display.niceDot($display)." \033[0;31merreur\033[0m\n";
	}else{
		echo $display.niceDot($display)." \033[0;32mok\033[0m\n";
	}

}

?>
