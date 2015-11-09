<?php

try{
	if(!is_file(substr(DB_PATH, 7))){
		throw new PDOException(DB_PATH.' n\'existe pas.');
	}
	$DB = new PDO(DB_PATH, '', '');
}catch(PDOException $e){
	$menuId = null;

	$smarty->assign('URL', URL);
	$smarty->assign('title', 'Isou');
	$smarty->assign('script', '');
	$smarty->assign('css', '');
	$smarty->assign('annonce', '<p id="annonce">La base de données d\'Isou est momentanément inaccessible. Veuillez réessayer ultérieurement.</p>');

	if(count($_GET)>0){
		$connexion_url = get_base_url('full', HTTPS).'&amp;';
	}else{
		$connexion_url = get_base_url('full', HTTPS).'?';
	}
	$smarty->assign('connexion_url', $connexion_url);

	$smarty->assign('page', $PAGE_NAME);
	$smarty->assign('is_admin', $IS_ADMIN);
	$smarty->assign('menu', $MENU);

	$smarty->display('html_head.tpl');
	$smarty->display('html_body_header.tpl');
	$smarty->display('html_body_footer.tpl');

	add_log(LOG_FILE, 'ISOU', 'ERROR_DB', $e->getMessage());

	// close pdo connection
	$DB = null;

	exit(0);
}

?>
