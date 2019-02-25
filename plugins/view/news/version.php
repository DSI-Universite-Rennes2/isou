<?php

$plugin = new stdClass();
$plugin->name = 'Actualité';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->label = $plugin->name;
$plugin->settings->route = 'actualite';

return $plugin;
