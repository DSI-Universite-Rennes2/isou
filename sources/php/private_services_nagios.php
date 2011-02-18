<?php

$arrayServices = array();
$arrayHosts = array();
$arrayNagios = array();

$sql = "SELECT idCategory, name".
		" FROM categories".
		" ORDER BY name";
$categories = $db->query($sql);

$optionCategories = array(0 => '');

while($category = $categories->fetch()){
	$optionCategories[$category[0]] = $category[1];
}

$handle = @fopen(STATUSDAT_URL, "r");
if ($handle) {
	while (!feof($handle)) {
		$tp =  trim(fgets($handle, 4096));
		if(preg_match('#hoststatus \{|servicestatus \{#',$tp)){
			$tag = substr($tp,0,-2);
			$continue=true;
			$host_name = "";
			$service_description = "";

			while(!feof($handle) && $continue){
				$tp =  trim(fgets($handle, 4096));
				if(!preg_match('#}#',$tp)){
					if(preg_match('#host_name=|service_description=#',$tp)){
						$split = explode('=',$tp);
						if($split[0] == 'host_name'){
							$host_name=$split[1];
						}else{
							$service_description=$split[1];
						}
					}
				}else{
					$continue=false;
				}
			}

			if($tag == 'servicestatus'){
				$host_name = $service_description.'@'.$host_name;
			}

			$sql = "SELECT idService, nameForUsers, idCategory".
					" FROM services".
					" WHERE name = '".$host_name."'".
					" AND nameForUsers IS NULL";
			$db->query($sql);

			if($nameForUsers = $db->query($sql)){
				$nameForUsers = $nameForUsers->fetch();
			}

			if(is_null($nameForUsers[0])){
				if($tag == 'servicestatus'){
					$arrayServices[count($arrayServices)] = $host_name;
				}else{
					$arrayHosts[count($arrayHosts)] = $host_name;
				}
			}

			$arrayNagios[count($arrayNagios)] = $host_name;
		}
	}
	fclose($handle);
}

sort($arrayServices);
sort($arrayHosts);



$sql = "SELECT COUNT(S.idService)".
		" FROM services S".
		" WHERE S.nameForUsers IS NULL";
$count = $db->query($sql);
$count = $count->fetch();
$countNagiosServices = $count[0];

$sql = "SELECT S.idService, S.name, S.nameForUsers, S.visible".
		" FROM services S".
		" WHERE S.nameForUsers IS NULL".
		" ORDER BY UPPER(S.name)";
$services = $db->query($sql);

$i = 0;
while($service = $services->fetchObject()){
	if((isset($_GET['modify']) && $_GET['modify'] == $service->idService) ||
		(isset($_POST['idService']) && $_POST['idService'] == $service->idService)){

		if($service->visible == 1){
			$service->css = 'longbox display';
		}else{
			$service->css = 'longbox hide';
		}
		$currentEdit = $service;
	}else{
		if($service->visible == 1){
			$service->css = 'display';
		}else{
			$sql = "SELECT D.idDependence".
			" FROM dependencies AS D, services AS S".
			" WHERE S.idService = ".$service->idService.
			" AND (D.idService = ".$service->idService.
			" OR D.idServiceParent = ".$service->idService.")";
			$dependy = $db->query($sql);

			if(!$dependy->fetch()){
				$service->css = 'unassign';
			}else{
				$service->css = 'hide';
			}
		}

		if(!in_array(stripslashes($service->name), $arrayNagios)){
			$service->css = 'nomorein';
		}
	}
	$i++;
	$nagiosServices[] = $service;
}

$smarty->assign('nagiosServices', $nagiosServices);
$smarty->assign('countNagiosServices', $countNagiosServices);
$smarty->assign('arrayServices', $arrayServices);
$smarty->assign('arrayHosts', $arrayHosts);
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
