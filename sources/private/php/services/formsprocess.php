<?php

if(isset($_GET['mask'])){
	$sql = "UPDATE services".
		" SET visible = 0".
		" WHERE idService = ?".
		" AND name = 'Service final'";
	$query = $DB->prepare($sql);
	if($query->execute(array($_GET['mask']))){
		add_log(LOG_FILE, NULL, 'UPDATE', 'Service #'.$_GET['mask'].' a été masqué');
	}
}

if(isset($_GET['show'])){
	$sql = "UPDATE services".
		" SET visible = 1".
		" WHERE idService = ?".
		" AND name = 'Service final'";
	$query = $DB->prepare($sql);
	if($query->execute(array($_GET['show']))){
		add_log(LOG_FILE, NULL, 'UPDATE', 'Service #'.$_GET['mask'].' est visible');
	}
}

if(isset($_POST['cancel'])){
	// nothing to do...
	unset($_POST['idService']);
}else{
	/* * * * * * * * * * * * * * * * * *
		Traitement d'un ajout
	* * * * * * * * * * * * * * * * * */

	if(isset($_POST['insert'])){
		$error = 'false';

		if(isset($_POST['category'])){
			$_POST['category'] = intval($_POST['category']);
			if($_POST['category']>0){
				$category = $_POST['category'];
			}else{
				$category = 1;
			}
		}else{
			$category = NULL;
		}

		if(isset($_POST['name'])){
			$name = htmlspecialchars($_POST['name']);
		}else{
			$name = 'Service final';
		}

		if(isset($_POST['url']) && filter_var($_POST['url'], FILTER_VALIDATE_URL)){
			$url = $_POST['url'];
		}else{
			$url = '';
		}

		if(!isset($_POST['nameForUsers'])){
			// nagios services
			$nameForUsers = NULL;
			$enable = 0;
			$visible = 0;
			$rssKey = NULL;
		}else{
			// isou services
			$nameForUsers = htmlspecialchars($_POST['nameForUsers']);
			$enable = 1;
			$visible = 1;
			$sql = "SELECT rssKey FROM services WHERE rssKey IS NOT NULL ORDER BY rssKey DESC";
			$rssKey = $DB->query($sql);
			$rssKey = $rssKey->fetch();
			$rssKey = $rssKey[0]+1;
		}

		// (isset($_POST['comment']))?$comment = $_POST['comment']:$comment = '';
		$comment = '';

		$sql = "INSERT INTO services (name, nameForUsers, url, state, comment, enable, visible, readonly, rssKey, idCategory)".
			" VALUES(?, ?, ?, 0, ?, ?, ?, 0, ?, ?)";
		$query = $DB->prepare($sql);
		if($query->execute(array($name,$nameForUsers,$url,$comment,$enable,$visible,$rssKey,$category))){
			$error = 'Le service '.$nameForUsers.' a été ajouté à la base';
			add_log(LOG_FILE, NULL, 'INSERT', 'Service #'.$DB->lastInsertId().' : VALUES('.$name.', '.$nameForUsers.', 0, '.$comment.', '.$enable.', '.$visible.', 0, '.$rssKey.', '.$category.')');
		}else{
			$error = 'Le service '.stripslashes($nameForUsers).' n\'a pas pu être ajouté à la base';
		}
	}

	/* * * * * * * * * * * * * * * * * *
		Traitement d'une suppression
	* * * * * * * * * * * * * * * * * */
	if(isset($_GET['delete'])){
		$_GET['delete'] = intval($_GET['delete']);
			if($_GET['delete'] > 0){
			$sql = "SELECT name, nameForUsers".
				" FROM services".
				" WHERE idService = ?";
			$query = $DB->prepare($sql);
			$query->execute(array($_GET['delete']));
			if($del_service = $query->fetch()){
				if(empty($del_service[1])){
					$nameService = $del_service[0];
				}else{
					$nameService = $del_service[1];
				}
			}else{
				$nameService = '#'.$_GET['delete'];
			}
		}
	}

	if(isset($_POST['delete'])){
		$commit = FALSE;
		$idService = intval($_POST['idDelService']);
		if($DB->beginTransaction()){
			$sql = "DELETE FROM services WHERE idService = ?";
			$query = $DB->prepare($sql);
			if($query->execute(array($idService))){
				if(isset($_GET['service'])){
					if($_GET['service'] === 'isou'){
						$sql = "DELETE FROM events WHERE idEvent = (SELECT idEvent FROM events_isou WHERE idService = ?)";
						$query = $DB->prepare($sql);
						if($query->execute(array($idService))){
							$sql = "DELETE FROM events_isou WHERE idService = ?";
							$query = $DB->prepare($sql);
							$flagDel = 1;
						}
					}elseif($_GET['service'] === 'nagios'){
						$sql = "DELETE FROM events WHERE idEvent = (SELECT idEvent FROM events_nagios WHERE idService = ?)";
						$query = $DB->prepare($sql);
						if($query->execute(array($idService))){
							$sql = "DELETE FROM events_nagios WHERE idService = ?";
							$query = $DB->prepare($sql);
							$flagDel = 1;
						}
					}

					if(isset($flagDel) && $query->execute(array($idService))){
						$sql = "DELETE FROM dependencies WHERE idService = ? OR idServiceParent = ?";
						$query = $DB->prepare($sql);
						if($query->execute(array($idService,$idService))){
							$commit = TRUE;
						}
					}
					unset($flagDel);
				}
			}
		}

		if(!isset($_POST['name'])){
			$_POST['name'] = '';
		}

		if($commit === TRUE){
			$DB->commit();
			$error = 'Le service '.$_POST['name'].' est maintenant supprimé dans la base, ainsi que ses évènements et dépendances liées';
			add_log(LOG_FILE, NULL, 'DELETE', 'Service #'.$idService);
		}else{
			$DB->rollBack();
			$error = 'Le service '.$_POST['name'].' n\'a pas pu être supprimé dans la base';
		}
	}

	/* * * * * * * * * * * * * * * * * *
		Traitement d'une modification
	* * * * * * * * * * * * * * * * * */
	if(isset($_POST['modify'])){
		$error = 'false';
		if(isset($_POST['category'])){
			$_POST['category'] = intval($_POST['category']);
		}else{
			$_POST['category'] = 0;
		}

		if(isset($_POST['idService'])){
			$_POST['idService'] = intval($_POST['idService']);
		}else{
			$_POST['idService'] = 0;
		}

		if(isset($_POST['name'])){
			$_POST['name'] = htmlspecialchars($_POST['name']);
		}else{
			$_POST['name'] = '';
		}
		
		if(isset($_POST['nameForUsers'])){
			$_POST['nameForUsers'] = htmlspecialchars($_POST['nameForUsers']);
		}else{
			$_POST['nameForUsers'] = '';
		}

		if(!isset($_POST['url']) || !filter_var($_POST['url'], FILTER_VALIDATE_URL)){
			$_POST['url'] = '';
		}

		$names = $_POST['nameForUsers'].$_POST['name'];
		if($_POST['idService']>0 && !empty($names)){
			$comment = '';

			if(!empty($_POST['nameForUsers']) && !empty($_POST['category'])){
				// service ISOU
				$sql = "UPDATE services".
					" SET nameForUsers = ?, url = ?, comment = ?, enable = 1, idCategory = ?".
					" WHERE idService = ?";
				$query = $DB->prepare($sql);
				if($query->execute(array($_POST['nameForUsers'],$_POST['url'],$comment,$_POST['category'],$_POST['idService']))){
					$error = 'Le service '.stripslashes($_POST['nameForUsers']).' a été modifié dans la base';
					add_log(LOG_FILE, NULL, 'UPDATE', 'Service #'.$_POST['idService'].' : SET nameForUsers = '.$_POST['nameForUsers'].', comment = '.$comment.', enable = 1, idCategory = '.$_POST['category']);
					unset($_POST['idService']);
				}else{
					$error = 'Le service '.stripslashes($_POST['nameForUsers']).' n\'a pas pu être mis à jour dans la base';
				}
			}else{
				// service NAGIOS
				$sql = "UPDATE services".
					" SET name = ?, nameForUsers = NULL, comment = ?, enable = 0, visible = 0, idCategory = NULL".
					" WHERE idService = ?";
				$query = $DB->prepare($sql);
				if($query->execute(array($_POST['name'], $comment, $_POST['idService']))){
					$error = 'Le service '.stripslashes($_POST['name']).' remplace le service précédemment sélectionné';
					add_log(LOG_FILE, NULL, 'UPDATE', 'Service #'.$_POST['idService'].' : SET nameForUsers = NULL, comment = '.$comment.', enable = 0, visible = 0, idCategory = '.$_POST['category']);
					unset($_POST['idService']);
				}else{
					$error = 'Le service '.stripslashes($_POST['name']).' n\'a pas pu être mis à jour dans la base';
				}
			}
		}else{
			$error = 'Remplir tous les champs';
		}
	}
}

?>
