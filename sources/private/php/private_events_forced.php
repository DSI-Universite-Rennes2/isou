<?php

$sql = "SELECT S.idService, S.name, S.nameForUsers, S.state".
		" FROM services S".
		" WHERE S.readonly = 1".
		" AND S.name = 'Service final'".
		" ORDER BY S.nameForUsers";
$services = $db->prepare($sql);
$services->execute();

$forcedservices = $services->fetchAll(PDO::FETCH_OBJ);

?>
