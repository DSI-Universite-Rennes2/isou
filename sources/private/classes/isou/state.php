<?php

namespace UniversiteRennes2\Isou;

class State{
	public $id;
	public $name;
	public $title;
	public $alternate_text;
	public $image;

	const OK = '0';
	const GREEN = '0';

	const WARNING = '1';
	const ORANGE = '1';

	const CRITICAL = '2';
	const RED = '2';

	const UNKNOWN = '3';
	const BLUE = '3';

	const CLOSE = '4';
	const WHITE = '4';

	public function __construct(){
		if(isset($this->idstate)){
			// instance PDO
			$this->id = $this->idstate;
			unset($this->idstate);
		}else{
			// instance manuelle
			$this->id = 0;
			$this->name = '';
			$this->title = '';
			$this->alternate_text = '';
			$this->image = '';
		}
	}

	public function get_flag_html_renderer(){
		return '<img src="'.URL.'/images/'.$this->image.'" alt="'.$this->alternate_text.'" width="16px" height="16px" />';
	}
}

?>
