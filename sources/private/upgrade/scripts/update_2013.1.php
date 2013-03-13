<?php

// Automne : Passer de l'heure d'été à l'heure normale - l'horloge est retardée d'une heure. Dernier dimanche du mois d'octobre
// Printemps : Passer de l'heure normale à l'heure d'été - l'horloge est avancée d'une heure. Dernier dimanche du mois de mars.
function is_summer_time($timestamp){
	$getdate = getdate($timestamp);
	if($getdate['mon'] > 3 && $getdate['mon'] < 10){
		// mois entre avril et septembre
		return True;
	}elseif($getdate['mon'] !== 3 || $getdate['mon'] !== 10){
		// mois avant mars ou après octobre
		return false;
	}else{
		// mois de mars ou octobre
		$sunday = $getdate['mday']-$getdate['wday']+7;
		if($getdate['mon'] === 3){
		   return $sunday > 31;
		}else{
		   return $sunday < 31;
		}
	}
}

function convert_timestamp_to_gmt($db, $year){
	$sql = "SELECT idEvent, beginDate, endDate FROM events";
	$query = $db->prepare($sql);
	$query->execute();
	$dates = $query->fetchAll(PDO::FETCH_OBJ);
	foreach($dates as $date){
		$beginDate = $date->beginDate;

		// conversion beginDate
		if(is_summer_time($date->beginDate)){
			$date->beginDate = $date->beginDate-3600; // +01:00
		}

		$getdate = getdate($date->beginDate);
		if($getdate['year'] !== $year){
			if($getdate['year'] < $year){
				$date->beginDate = mktime(0,0,0,1,1,$year);
			}else{
				$date->beginDate = mktime(23,59,59,12,31,$year);
			}
		}

		// conversion endDate
		if($date->endDate !== NULL){
			$date->endDate = $date->beginDate+($date->endDate-$beginDate);

			$getdate = getdate($date->endDate);
			if($getdate['year'] !== $year){
				$date->endDate = mktime(23,59,59,12,31,$year);
			}
		}

		// mise à jour des nouvelles valeurs
		$sql = "UPDATE events SET beginDate=?, endDate=? WHERE idEvent=?";
		$query = $db->prepare($sql);
		$query->execute(array(strftime('%Y-%m-%dT%H:%M', $date->beginDate), strftime('%Y-%m-%dT%H:%M', $date->endDate), $date->idEvent));
	}
}

// conversion des vieilles bases de données
echo "\n\033[0;32mNote: cette opération peut prendre quelques minutes...\033[0m\n";
$current_year = getdate();
$current_year = $current_year['year'];
for($y=2009;$y<$current_year;$y++){
	$db_path = BASE.'/database/isou-'.$y.'.sqlite3';
	if(is_file($db_path)){
		$display = 'Conversion des dates pour la base "isou-'.$y.'.sqlite3"';
		echo $display.niceDot($display);
		try{
			$old_db = new PDO('sqlite:'.$db_path, '', '');
		}catch(PDOException $e){
			echo " \033[0;31merreur\033[0m\n";
			echo "\tErreur retournée : ".$e->getMessage()."\n";
			// close pdo connection
			$old_db = null;
			continue;
		}
		convert_timestamp_to_gmt($old_db, $y);
		echo " \033[0;32mok\033[0m\n";
	}
}

// conversion de la base de données courantes
$display = 'Conversion des dates pour la base "isou.sqlite3"';
echo $display.niceDot($display);
convert_timestamp_to_gmt($db, $current_year);
echo " \033[0;32mok\033[0m\n\n";

// afficher le changelog
echo "Voulez-vous afficher le changelog ? (y/n)\n";
$stdin = trim(fgets(STDIN));
if(strtolower($stdin) !== 'n'){
	echo "\nChangelog de la version 2013.1:\n";
	echo "\t- correction du bug provoqué lors des changements d'heure hiver/été (bug reporté par l'université de La Rochelle)\n";
	echo "\t- correction de bugs du script d'installation (bug reporté par l'université de Bretagne occidentale)\n";
	echo "\t- suppression de la possibilité de mettre à jour l'application par l'interface web\n";
	echo "\t- utilisation de la variable permettant de ne pas afficher les services dont l'interruption\n";
	echo " est \"inférieur à\" dans l'affichage du flux RSS\n";
}

?>
