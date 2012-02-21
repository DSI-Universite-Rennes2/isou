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
		if($file != '.' && $file != '..' && $file != '.svn' && $file != '.git'){
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

// add function addDir to PharData class
class isouPharData extends PharData{
	// $source: path to archive
	// $destination : path in archive
	// $exclude : exclude dir (use path like $destination format)
	public function addDir($source, $destination, $exclude=array()){
		// add an empty dir in the archive
		if(is_dir($source) && in_array($destination, $exclude) === FALSE){
			if(($dir = opendir($source)) === FALSE){
				return FALSE;
			}

			$this->addEmptyDir($destination);
		}else{
			return FALSE;
		}

		while(($file = readdir($dir)) !== FALSE){
			if(in_array($file, array_merge(array('.', '..', '.svn', '.git'), $exclude)) === FALSE){
				if(is_dir($source.'/'.$file)){
					// recall addDir to create dir and to add files
					$this->addDir($source.'/'.$file, $destination.'/'.$file, $exclude);
				}else{
					// add file to archive
					$this->addFile($source.'/'.$file, $destination.'/'.$file);
				}
			}
		}

		closedir($dir);

		return TRUE;
	}
}

?>
