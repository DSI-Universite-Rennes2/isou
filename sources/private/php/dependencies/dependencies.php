<?php

	$TITLE = NAME.' - Administration des Dépendances';

	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery-min.js');
	$SCRIPTS[] = new Isou\Helpers\Script(URL.'/scripts/jquery_dependencies.js');

	$update = '';

	require PRIVATE_PATH.'/php/dependencies/formsprocess.php';

	/* génération du champs select avec tous les services enfants */
	$sql = "SELECT S.idService, S.nameForUsers, S.name ".
			" FROM services AS S".
			" WHERE S.nameForUsers IS NOT NULL".
			" ORDER BY UPPER(S.nameForUsers)";
	$services = $DB->query($sql);
	$optionChildService = array();
	while($service = $services->fetchObject()){
		$optionChildService[$service->idService] = $service->nameForUsers;
	}
	$smarty->assign('optionChildService', array('Services Isou ('.count($optionChildService).' services)' => $optionChildService));

	/* génération du champs select avec tous les services parents */
	$sql = "SELECT idService, name, nameForUsers".
			" FROM services".
			" WHERE idService NOT IN(SELECT S.idService ".
				" FROM services AS S".
				" WHERE S.nameForUsers IS NOT NULL)".
			" ORDER BY UPPER(name)";
	$services = $DB->query($sql);
	$optionNagiosService = array();
	while($service = $services->fetchObject()){
		if(is_null($service->nameForUsers)){
			$optionNagiosService[$service->idService] = $service->name;
		}else{
			$optionNagiosService[$service->idService] = $service->name.' ('.$service->nameForUsers.')';
		}
	}

	$sql = "SELECT S.idService, S.nameForUsers, S.name ".
			" FROM services AS S".
			" WHERE S.nameForUsers IS NOT NULL".
			" ORDER BY UPPER(S.nameForUsers)";
	$services = $DB->query($sql);
	$optionIsouService = array();
	while($service = $services->fetchObject()){
		$optionIsouService[$service->idService] = $service->nameForUsers;
	}

	$optionNagiosService = array('Services Nagios ('.count($optionNagiosService).' services)' => $optionNagiosService);
	$optionIsouService = array('Services Isou ('.count($optionIsouService).' services)' => $optionIsouService);
	$smarty->assign('optionParentService', $optionNagiosService+$optionIsouService);

	$optionState = array(1 => 1, 2 => 2);
	$smarty->assign('optionState', $optionState);

	/* * * * * * * * * * * * * * * * * * * * *
		FORMULAIRE D'AJOUT DES DONNEES
	* * * * * * * * * * * * * * * * * * * * * */

	$stateOfParent = 1;
	$newStateForChild = 1;
	$parentService = 0;
	$childService = 0;

	// memorise le dernier ajout de dependance
	if(isset($_POST['insert'])){
		$parentService = $_POST['parentService'];
			$childService = $_POST['childService'];
			$stateOfParent = $_POST['stateOfParent'];
			$newStateForChild = $_POST['newStateForChild'];
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
		FORMULAIRE DE MODIFICATION/SUPPRESSION DES DONNEES
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	if(isset($_GET['sort']) && $_GET['sort'] == 'child'){
		$sql = "SELECT D.idDependence, D.idService, D.idServiceParent, D.stateOfParent, D.newStateForChild, D.message".
				" FROM dependencies AS D, services AS S, categories AS C".
				" WHERE S.idService = D.idService".
				" AND C.idCategory = S.idCategory".
				" ORDER BY UPPER(C.name), UPPER(S.nameForUsers)";
	}else{
		$sql = "SELECT D.idDependence, D.idService, D.idServiceParent, D.stateOfParent, D.newStateForChild, D.message".
			" FROM dependencies AS D, services AS S, categories AS C".
			" WHERE S.idService = D.idService".
			" AND C.idCategory = S.idCategory".
			" ORDER BY UPPER(S.nameForUsers), D.newStateForChild";
	}
	$dependencies = $DB->query($sql);
	$dependencies = $dependencies->fetchAll();

	$sql = "SELECT DISTINCT S.idService, S.nameForUsers".
			" FROM dependencies AS D, services AS S".
			" WHERE S.idService = D.idService".
			" ORDER BY UPPER(S.nameForUsers)";

	$result = $DB->query($sql);

	$i = 0;
	$services = array();

	while($service = $result->fetchObject()){
		$sql = "SELECT D.idDependence, D.idService, D.idServiceParent, D.stateOfParent, D.newStateForChild, D.message".
			" FROM dependencies AS D, services AS S, categories AS C".
			" WHERE S.idService = D.idService".
			" AND C.idCategory = S.idCategory".
			" AND S.idService = ".$service->idService.
			" ORDER BY UPPER(S.nameForUsers), D.newStateForChild";

		$dependencies = $DB->query($sql);
		$j = 0;

		$service->dependency1 = array();
		$service->dependency2 = array();

		while($dependency = $dependencies->fetchObject()){
			$sql = "SELECT S.nameForUsers, S.name".
					" FROM services AS S".
					" WHERE S.idService = ".$dependency->idServiceParent;
			$parent = $DB->query($sql);
			$parent = $parent->fetchAll();

			if(is_null($parent[0][0])){
				$dependency->name = $parent[0][1];
			}else{
				$dependency->name = $parent[0][0];
			}

			if($dependency->newStateForChild === '1'){
				$service->dependency1[] = $dependency;
			}else{
				$service->dependency2[] = $dependency;
			}

			$j++;
		}
		$i++;
		$services[] = $service;
	}

	$smarty->assign('update', $update);
	$smarty->assign('services', $services);

	$template = 'dependencies/dependencies.tpl';

?>
