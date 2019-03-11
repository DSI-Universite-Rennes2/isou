<?php

namespace Isou\Helpers;

class Script {
    public $src;
    public $type;

    public function __construct($src, $type = 'text/javascript') {
        $this->src = $src;
        $this->type = $type;
    }
}
