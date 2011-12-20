<?php

	$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/categories.css" media="screen" />';
	$script = '';
	$title = NAME.' - Administration des Catégories';

	/* * * * * * * * * * * * * * * * * *
		Traitement d'un ajout
	* * * * * * * * * * * * * * * * * */
	if(isset($_POST['insert'])){
		if(isset($_POST['name']) && !empty($_POST['name'])){

			$name = $_POST['name'];

			$count = 0;
			$sql = "SELECT count(*)".
					" FROM categories";
			$count = $db->query($sql);
			$count = $count->fetch();
			$count = $count[0]+1;

			$sql = "INSERT INTO categories (name, position)".
					" VALUES(?, ?)";
			$query = $db->prepare($sql);
			if($query->execute(array($name,$count))){
				$error = 'La catégorie a été insérée avec succès.';
			}else{
				$error = 'La catégorie n\'a pas pu être insérée.';
			}
		}else{
			$error = 'Veuillez remplir le champs "Nom de la catégorie"';
		}
	}

	/* * * * * * * * * * * * * * * * * *
		Traitement d'une modification
	* * * * * * * * * * * * * * * * * */
	if(isset($_POST['modify'])){
		if(isset($_POST['idCategory']) && is_numeric(0+$_POST['idCategory']) && $_POST['idCategory']>0 &&
		isset($_POST['name']) && !empty($_POST['name'])){
			$idCategory = $_POST['idCategory'];
			$name =  $_POST['name'];

			$sql = "UPDATE categories ".
				" SET name = ?".
				" WHERE idCategory = ?";
			$query = $db->prepare($sql);
			if(!$query->execute(array($name, $idCategory))){
				$error = 'La base n\'a pas pu être mis à jour';
			}else{
				unset($_POST['idCategory']);
				$error = 'La base a été mise à jour avec succès';
			}
		}else{
			$error = 'Veuillez donne un nom à la catégorie';
		}
	}

	$sql = "SELECT count(*)".
			" FROM categories";
	$count = $db->query($sql);
	$count = $count->fetch();
	$count = $count[0];

	/* * * * * * * * * * * * * * * * * *
		Traitement du positionnement
	* * * * * * * * * * * * * * * * * */
	if(isset($_GET['action'])){
		$id = $_GET['id'];

		$sql = "SELECT position".
				" FROM categories".
				" WHERE idCategory = ?";
		$query = $db->prepare($sql);
		$query->execute(array($id));
		$position = $query->fetch();

		if(($_GET['action'] == 'up' && $position[0] > 1) || ($_GET['action'] == 'down' && $position[0] < $count)){

			if($_GET['action'] == 'up'){
				$op1 = "-";
				$op2 = "+";
			}else{
				$op1 = "+";
				$op2 = "-";
			}

			$commit = false;
			if($db->beginTransaction()){
				$sql = "UPDATE categories".
				" SET position=position".$op1."1".
				" WHERE idCategory = ?";
				$query = $db->prepare($sql);
				if($query->execute(array($id))){
					$sql = "UPDATE categories".
							" SET position=position".$op2."1".
							" WHERE position = ".$position[0].$op1."1".
							" AND idCategory != ?";
					$query = $db->prepare($sql);
					if($query->execute(array($id))){
						$commit = true;
					}
				}
			}

			if($commit){
				$db->commit();
				$error = 'La base a été mise à jour avec succès';
			}else{
				$db->rollBack() ;
				$error = 'La base n\'a pas pu être mis à jour';
			}
		}else{
			$error = 'La base n\'a pas pu être mis à jour';
		}
	}

	/* * * * * * * * * * *
	 * Données des catégories
	 * * * * * * * * * * * */

	$sql = "SELECT idCategory, name, position".
		" FROM categories".
		" ORDER BY position";
	$results = $db->query($sql);
	$categories = array();
	while($categorie = $results->fetchObject()){
		$categories[] = $categorie;
	}

	if(isset($error)){
		$smarty->assign('error', $error);
	}

	$smarty->assign('count', $count);
	$smarty->assign('categories', $categories);

?>