<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace Isou\Helpers;

/**
 * Classe décrivant une balise HTML script.
 */
class Script {
    /**
     * Attribut src.
     *
     * @var string
     */
    public $src;

    /**
     * Attribut type.
     *
     * @var string
     */
    public $type;

    /**
     * Constructeur de la classe.
     *
     * @param string $src Attribut src.
     * @param string $type Attribut type.
     *
     * @return void
     */
    public function __construct(string $src, string $type = 'text/javascript') {
        $this->src = $src;
        $this->type = $type;
    }
}
