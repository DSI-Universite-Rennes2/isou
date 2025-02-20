<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$plugin = new stdClass();
$plugin->name = 'Nagios';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->statusdat_path = '/var/share/nagios/status.dat';

return $plugin;
