<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$plugin = new stdClass();
$plugin->name = 'Isou';
$plugin->version = '1.1.0';

$plugin->settings = new stdClass();
$plugin->settings->tolerance = 120;
$plugin->settings->grouping = false;

return $plugin;
