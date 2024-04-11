<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$plugin = new stdClass();
$plugin->name = 'Zabbix';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->zabbix_url = '';
$plugin->settings->zabbix_api_token = '';
$plugin->settings->zabbix_tags = '';

return $plugin;
