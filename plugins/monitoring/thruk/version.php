<?php

$plugin = new stdClass();
$plugin->name = 'Thruk';
$plugin->version = '1.0.0';

$plugin->settings = new stdClass();
$plugin->settings->thruk_path = '';
$plugin->settings->thruk_username = '';
$plugin->settings->thruk_password = '';

return $plugin;
