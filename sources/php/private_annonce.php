<?php

$css = '<link rel="stylesheet" type="text/css" href="'.URL.'/css/annonce.css" media="screen" />';
$script = '';
$title = NAME.' - Annonce';

$error = '';

if(isset($_POST['submit'])){
	$message = $_POST['message'];
	(isset($_POST['afficher']))?$afficher=1:$afficher=0;

	$sql = "UPDATE annonce SET message = ?, afficher = ?";
	$query = $db->prepare($sql);
	if($query->execute(array($message, $afficher))){
		if($afficher === 1){
			$error = 'L\'annonce a bien été enregistrée.';
		}else{
			$error = 'L\'annonce a bien été retirée.';
		}
		add_log(LOG_FILE, phpCAS::getUser(), 'UPDATE', 'Modification de l\'annonce ');
	}else{
		$error = 'La modification n\'a pas été enregistrée !';
	}

	$message = stripslashes($_POST['message']);
	($afficher == 1)?$afficher = ' checked=checked':$afficher = '';

}else{

	$sql = "SELECT * FROM annonce";
	if($annonce = $db->query($sql)){
		$annonce = $annonce->fetch();
		($annonce[1] == 1)?$afficher = ' checked=checked':$afficher = '';
		$message = stripslashes($annonce[0]);
	}else{
		$afficher = '';
		$message = '';
	}
}

$annonce = '';

$smarty->assign('error', $error);
$smarty->assign('message', $message);
$smarty->assign('afficher', $afficher);

?>
