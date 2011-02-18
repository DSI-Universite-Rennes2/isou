<?php

// function cp équivalent unix à cp -r ./dir /tmp
// ignore les répertoires/fichiers nommés .svn
function cp($source, $destination){
	if(is_dir($source)){
		if(($dir = opendir($source)) === FALSE){
			return FALSE;
		}

		if(!is_dir($destination)){
			if(@mkdir($destination) === FALSE){
				return FALSE;
			}
		}
	}else{
		return copy($source, $destination);
	}

	$cp = TRUE;
	while(($file = readdir($dir)) !== FALSE){
		if($file != '.' && $file != '..' && $file != '.svn'){
			if(is_dir($source.'/'.$file)){
				$cp = cp($source.'/'.$file, $destination.'/'.$file);
			}else{
				$cp = copy($source.'/'.$file, $destination.'/'.$file);
			}

			if($cp === FALSE){
				return FALSE;
			}
		}
	}

	closedir($dir);

	return TRUE;
}

// ajoute des points pour aligner les retours d'étape
function niceDot($string, $len = 125){
	$strlen = strlen($string);
	if($strlen < $len){
		return str_repeat('.', $len-$strlen);
	}else{
		return str_repeat('.', 3);
	}
}

?>
