<?php

function get_parents($idChild){
	$parents = array();

	try {
		$dbr = new PDO(DB_PATH, '', '');
		$sql = "SELECT DISTINCT D.idServiceParent, S.name, S.nameForUsers, S.state".
		" FROM dependencies AS D, services AS S".
		" WHERE S.idService = D.idServiceParent".
		" AND D.idService = :0".
		" ORDER BY UPPER(S.name), UPPER(S.nameForUsers)";

		$services = $dbr->prepare($sql);
		$services->execute(array($idChild));
		while($service = $services->fetch(PDO::FETCH_OBJ)){
			$tmpParents = get_parents($service->idServiceParent);
			if(count($tmpParents) > 0){
				$service->parents = $tmpParents;
			}
			$parents[] = $service;
		}

	} catch (PDOException $e) {
		add_log(LOG_FILE, NULL, 'ERROR_DB', $e->getMessage());
	}

	// close pdo connection
	$dbr = null;

	return $parents;
}

$sql = "SELECT idCategory, name".
		" FROM categories".
		" ORDER BY position";
$categories = $DB->query($sql);
$optionCategories = array();
while($category = $categories->fetch()){
	$optionCategories[$category[0]] = $category[1];
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	FORMULAIRE DE MODIFICATION/SUPPRESSION DES DONNEES
* * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$sql = "SELECT S.idService, S.name, S.nameForUsers, S.url, S.state, S.comment, S.readonly, S.visible, S.idCategory, C.name AS category".
		" FROM services S, categories C".
		" WHERE C.idCategory = S.idCategory".
		" ORDER BY C.position, UPPER(S.nameForUsers)";
$services = $DB->query($sql);

$currentCategory = '';

$categories = array();

$i = 0;
while($service = $services->fetchObject()){
	if((isset($_GET['modify']) && $_GET['modify'] == $service->idService) ||
		(isset($_POST['idService']) && $_POST['idService'] == $service->idService)){
		$currentEdit = $service;
	}

	$parents = get_parents($service->idService);
	if($parents == ''){
		$service->css = 'unassign';
		$service->parents = array();
	}else{
		$service->css = 'body';
		$service->parents = $parents;
	}

	if($service->readonly === '0'){
		$service->forced = 'Non';
	}else{
		$service->forced = 'Oui';
		$service->css = 'forced';
	}

	if($currentCategory !== $service->category){
		$currentCategory = $service->category;
		$categories[] = new stdClass();
		$categories[count($categories)-1]->name = $service->category;
		$categories[count($categories)-1]->services = array();
	}

	$categories[count($categories)-1]->services[] = $service;

	$i++;
}

$smarty->assign('categories', $categories);
$smarty->assign('optionCategories', $optionCategories);
if(isset($currentEdit)){
	$smarty->assign('currentEdit', $currentEdit);
}

if(isset($error)){
	$smarty->assign('error', $error);
}

if(isset($_GET['delete'])){
	$smarty->assign('nameService', $nameService);
}

?>
