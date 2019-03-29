<?php

$plugin = new stdClass();
$plugin->name = 'Isou';
$plugin->version = '1.1.0';

$plugin->settings = new stdClass();
$plugin->settings->tolerance = 120;
$plugin->settings->grouping = false;

return $plugin;
