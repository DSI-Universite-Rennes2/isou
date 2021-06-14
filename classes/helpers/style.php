<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace Isou\Helpers;

/**
 * Classe décrivant une balise HTML style.
 */
class Style {
    /**
     * Attribut url.
     *
     * @var string
     */
    public $url;

    /**
     * Attribut media.
     *
     * @var string
     */
    public $media;

    /**
     * Attribut rel.
     *
     * @var string
     */
    public $rel;

    /**
     * Constructeur de la classe.
     *
     * @param string $url Attribut url.
     * @param string $media Attribut media.
     * @param string $rel Attribut rel.
     *
     * @return void
     */
    public function __construct(string $url, string $media = 'screen', string $rel = 'stylesheet') {
        $this->url = $url;
        $this->media = $media;
        $this->rel = $rel;
    }
}
