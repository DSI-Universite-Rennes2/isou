<?php

namespace UniversiteRennes2\Isou;

class State{
	public $id;
	public $name;
	public $title;
	public $alternate_text;
	public $image;

	const STATE_OK = '0';
	const STATE_GREEN = '0';

	const STATE_WARNING = '1';
	const STATE_ORANGE = '1';

	const STATE_CRITICAL = '2';
	const STATE_RED = '2';

	const STATE_UNKNOWN = '3';
	const STATE_BLUE = '3';

	const STATE_CLOSE = '4';
	const STATE_WHITE = '4';

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
