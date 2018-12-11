<?php

$plugin = new stdClass();
$plugin->name = 'Nagios';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->statusdat_path = '/var/share/nagios/status.dat';

return $plugin;
