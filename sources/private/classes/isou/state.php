<?php

namespace UniversiteRennes2\Isou;

class State{
	const OK = '0';
	const GREEN = '0';

	const WARNING = '1';
	const ORANGE = '1';

	const CRITICAL = '2';
	const RED = '2';

	const UNKNOWN = '3';
	const BLUE = '3';

	const CLOSED = '4';
	const WHITE = '4';

	public $id;
	public $name;
	public $title;
	public $alternate_text;
	public $image;

	public static $STATES = array(
		self::OK => 'Fonctionne',
		self::WARNING => 'Instable',
		self::CRITICAL => 'Indisponible',
		self::UNKNOWN => 'Indéterminé',
		self::CLOSED => 'Fermé'
		);

	public function __construct(){
		if(!isset($this->id)){
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
