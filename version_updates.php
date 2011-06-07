<?php

// typage du numéro de version
$version = (float) substr(VERSION, 0, strpos(VERSION, '.')+1).str_replace('.', '', substr(VERSION, strpos(VERSION, '.')+1));

// mise à jour pour les versions antérieures à la 0.9.5
if($version <= 0.95){
	echo "\nQuelles sont les adresses mails devant recevoir des alertes mails (séparés par des virgules) ?\n";
	echo "Défaut: \033[1;34mvide\033[0m\n";
	echo "exemple : \033[1;30mexample1@example.com, example2@example.com\033[0m\n";
	$ADMIN_MAILS = trim(fgets(STDIN));
	if(empty($ADMIN_MAILS)){
		$ADMIN_MAILS = 'array()';
	}else{
		$mails = explode(',', $ADMIN_MAILS);
		$ADMIN_MAILS = '';
		foreach($mails as $mail){
			$ADMIN_MAILS .= '\''.trim($mail).'\',';
		}
		$ADMIN_MAILS = 'array('.substr($ADMIN_MAILS, 0, -1).')';
	}
	$ADMIN_MAILS = "// tableau contenant le mail des administrateurs d'ISOU\n".$ADMIN_MAILS;
	$LOG_LEVEL = "// niveau verbeux des logs\n// valeurs possibles : 0=muet, 1=production, 2=debug\ndefine('LOG_LEVEL', 1);";

	$cfg = file_get_contents($config);
	$cfg = str_replace("?>", $ADMIN_MAILS."\n".$LOG_LEVEL."\n\n?>", $cfg);
	file_put_contents($config, $cfg);
}

?>
