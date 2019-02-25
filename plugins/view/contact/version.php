<?php

$plugin = new stdClass();
$plugin->name = 'Contact';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->label = $plugin->name;
$plugin->settings->route = strtolower($plugin->name);
$plugin->settings->message = '<p class="alert alert-info">Page en construction</p>';

return $plugin;
