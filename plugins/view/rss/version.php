<?php

$plugin = new stdClass();
$plugin->name = 'RSS';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->label = 'Flux RSS';
$plugin->settings->route = 'rss';

return $plugin;
