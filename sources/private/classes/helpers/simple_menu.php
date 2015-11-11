<?php

namespace Isou\Helpers;

class SimpleMenu{
	public $label;
	public $title;
	public $url;
	public $selected;

	public function __construct($label, $title, $url=NULL, $selected=FALSE){
		$this->label = $label;
		$this->title = $title;
		$this->url = $url;
		$this->selected = $selected;
	}
}

?>
