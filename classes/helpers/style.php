<?php

namespace Isou\Helpers;

class Style{
    public $url;
    public $media;
    public $rel;

    public function __construct($url, $media = 'screen', $rel = 'stylesheet') {
        $this->url = $url;
        $this->media = $media;
        $this->rel = $rel;
    }
}
