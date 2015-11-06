<?php


/* * * * * * * * * * * * * * * * * *
	Annulation
* * * * * * * * * * * * * * * * * */
if(isset($_POST['cancel'])){
	header('Location: '.URL.'/index.php/dependances?S='.$_GET['S']);
	exit();
}

require PRIVATE_PATH.'/classes/htmlpurifier/library/HTMLPurifier.auto.php';
$HTMLPurifier = new HTMLPurifier();

if(isset($_POST['stateOfParent'])){
	$_POST['stateOfParent'] = intval($_POST['stateOfParent']);
}else{
	$_POST['stateOfParent'] = 0;
}

if(isset($_POST['newStateForChild'])){
	$_POST['newStateForChild'] = intval($_POST['newStateForChild']);
}else{
	$_POST['newStateForChild'] = 0;
}

if(isset($_POST['parentService'])){
	$_POST['parentService'] = intval($_POST['parentService']);
}else{
	$_POST['parentService'] = 0;
}

if(isset($_POST['childService'])){
	$_POST['childService'] = intval($_POST['childService']);
}else{
	$_POST['childService'] = 0;
}

if(!isset($_POST['description']) || empty($_POST['description'])){
	$_POST['description'] = NULL;
}else{
	$_POST['description'] = $HTMLPurifier->purify($_POST['description']);	
}

/* * * * * * * * * * * * * * * * * *
  	Traitement d'un ajout
* * * * * * * * * * * * * * * * * */
if(isset($_POST['insert'])){
	if($_POST['stateOfParent']>0 && $_POST['newStateForChild']>0 && $_POST['parentService']>0 && $_POST['childService']>0){
		if(isset($_POST['both']) && $stateOfParent == $newStateForChild){
			$sql = "INSERT INTO dependencies (message, newStateForChild, stateOfParent, idService, idServiceParent)".
					" VALUES(?, 1, 1, ?, ?)";
			$query = $db->prepare($sql);
			if($query->execute(array($_POST['description'], $_POST['childService'], $_POST['parentService']))){
				$sql = "INSERT INTO dependencies (message, newStateForChild, stateOfParent, idService, idServiceParent)".
					" VALUES(?, 2, 2, ?, ?)";
				$query = $db->prepare($sql);
				if($query->execute(array($_POST['description'], $_POST['childService'], $_POST['parentService']))){
					$error = 'Les dépendances ont été insérées avec succès.';
					add_log(LOG_FILE, NULL, 'INSERT', 'Dépendance #'.$db->lastInsertId().' : VALUES('.$description.', 1 et 2, 1 et 2, '.$childService.', '.$parentService.')');
				}else{
					$_POST['insert'] = NULL;
					$error = 'La dépendance avec les états à 2-2 n\'a pas pu être insérée.';
				}
			}else{
				$_POST['insert'] = NULL;
				$error = 'Les dépendances n\'ont pas pu être insérées.';
			}

		}else{
			$sql = "INSERT INTO dependencies (message, newStateForChild, stateOfParent, idService, idServiceParent)".
					" VALUES(?, ?, ?, ?, ?)";
			$query = $db->prepare($sql);
			if($query->execute(array($_POST['description'], $_POST['newStateForChild'], $_POST['stateOfParent'], $_POST['childService'], $_POST['parentService']))){
				$error = 'La dépendance a été insérée avec succès.';
				add_log(LOG_FILE, NULL, 'INSERT', 'Dépendance #'.$db->lastInsertId().' : VALUES('.$description.', '.$newStateForChild.', '.$stateOfParent.', '.$childService.', '.$parentService.')');
			}else{
				$_POST['insert'] = NULL;
				$error = 'La dépendance n\'a pas pu être insérée.';
			}
		}

	}else{
		$error = 'Remplir tous les champs (sauf "description" qui est facultatif)';
	}
	$update = '<p id="update">'.$error.'</p>';
}

/* * * * * * * * * * * * * * * * * *
  	Traitement d'une suppression
* * * * * * * * * * * * * * * * * */
if(isset($_GET['delete'])){
	$_GET['delete'] = intval($_GET['delete']);
	if($_GET['delete']>0){
		$update = '<div id="update">';
		$update .= '<p>Voulez-vous vraiment effacer la dépendance #'.$_GET['delete'].' ?</p>';
		$update .= '<form action="'.URL.'/index.php/dependances#'.$_GET['S'].'" method="post">';
		$update .= '<p><input type="submit" name="delete" value="Oui"> <input type="submit" value="Non">';
		$update .= '<input class="hidden" type="hidden" name="idDelDependence" value="'.$_GET['delete'].'"></p>';
		$update .= '</form>';
		$update .= '</div>';
	}
}

if(isset($_POST['delete']) && isset($_POST['idDelDependence'])){
	$_POST['idDelDependence'] = intval($_POST['idDelDependence']);
	if($_POST['idDelDependence']>0){
		$sql = "DELETE FROM dependencies ".
				" WHERE idDependence = ?";
		$query = $db->prepare($sql);
		if($query->execute(array($_POST['idDelDependence']))){
			$error = 'La dépendance #'.$_POST['idDelDependence'].' a été supprimée avec succès.';
			add_log(LOG_FILE, NULL, 'DELETE', 'Dépendance #'.$_POST['idDelDependence']);
		}else{
			$error = 'La dépendance #'.$_POST['idDelDependence'].' n\'a pas pu être supprimée.';
		}
	}else{
		$error = 'La dépendance #'.$_POST['idDelDependence'].' n\'a pas pu être supprimée.';
	}
	$update = '<p id="update">'.$error.'</p>';
}

/* * * * * * * * * * * * * * * * * *
  	Traitement d'une modification
* * * * * * * * * * * * * * * * * */

if(isset($_POST['modify'])){
	$error = 'false';
	if($_POST['stateOfParent']>0 && $_POST['newStateForChild']>0 && $_POST['parentService']>0 && $_POST['childService']>0 && $_POST['idDependence']>0){
		$sql = "UPDATE dependencies ".
				" SET message = ?, newStateForChild = ?, stateOfParent = ?, idService = ?, idServiceParent = ?".
				" WHERE idDependence = ?";
		$query = $db->prepare($sql);
		if($query->execute(array($_POST['description'],$_POST['newStateForChild'], $_POST['stateOfParent'], $_POST['childService'], $_POST['parentService'], $_POST['idDependence']))){
			$error = 'La base a été mise à jour avec succès';
			add_log(LOG_FILE, NULL, 'UPDATE', 'Dépendance #'.$_POST['idDependence'].' : SET message = '.$_POST['description'].', newStateForChild = '.$_POST['newStateForChild'].', stateOfParent = '.$_POST['stateOfParent'].', idService = '.$_POST['childService'].', idServiceParent = '.$_POST['parentService']);
			unset($_POST['idDependence']);
		}else{
			$error = 'La base n\'a pas pu être mis à jour';
		}
	}else{
		$error = 'Remplir tous les champs (sauf "description" qui est facultatif)';
	}
	$update = '<p id="update">'.$error.'</p>';
}

?>
