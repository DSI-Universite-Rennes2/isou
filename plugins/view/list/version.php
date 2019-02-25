<?php

$plugin = new stdClass();
$plugin->name = 'Liste';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->label = $plugin->name;
$plugin->settings->route = strtolower($plugin->name);

return $plugin;
