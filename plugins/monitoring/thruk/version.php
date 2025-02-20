<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$plugin = new stdClass();
$plugin->name = 'Thruk';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->thruk_path = '';
$plugin->settings->thruk_username = '';
$plugin->settings->thruk_password = '';

return $plugin;
