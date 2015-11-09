<?php

$TITLE = NAME.' - Annonce';

$STYLES[] = new Isou\Helpers\Style(URL.'/styles/classic/annonce.css');

$error = '';

if(isset($_POST['submit'])){
	require PRIVATE_PATH.'/classes/htmlpurifier/library/HTMLPurifier.auto.php';
	$HTMLPurifier = new HTMLPurifier();
	$message = $HTMLPurifier->purify($_POST['message']);

	(isset($_POST['afficher']))?$afficher=1:$afficher=0;

	$sql = "UPDATE annonce SET message = ?, afficher = ?";
	$query = $DB->prepare($sql);
	if($query->execute(array($message, $afficher))){
		if($afficher === 1){
			$error = 'L\'annonce a bien été enregistrée.';
		}else{
			$error = 'L\'annonce a bien été retirée.';
		}
		add_log(LOG_FILE, NULL, 'UPDATE', 'Modification de l\'annonce ');
	}else{
		$error = 'La modification n\'a pas été enregistrée !';
	}

	$message = stripslashes($_POST['message']);
	($afficher == 1)?$afficher = ' checked=checked':$afficher = '';

}else{

	$sql = "SELECT * FROM annonce";
	if($annonce = $DB->query($sql)){
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

$template = 'announcement/announcement.tpl';

?>
