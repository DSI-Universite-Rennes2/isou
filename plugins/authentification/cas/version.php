<?php

// Hack: instantiate phpCAS to initialize constants.
phpCAS::getVersion();

$plugin = new stdClass();
$plugin->name = 'Authentification CAS';
$plugin->version = '1.0.1';

$plugin->settings = new stdClass();
$plugin->settings->cas_protocol = CAS_VERSION_2_0;
$plugin->settings->cas_host = '';
$plugin->settings->cas_path = '';
$plugin->settings->cas_port = 443;
$plugin->settings->cas_certificate_path = '';

$plugin->settings->cas_ldap_uri = 'ldap://ldap.example.com:389';
$plugin->settings->cas_ldap_username = '';
$plugin->settings->cas_ldap_password = '';
$plugin->settings->cas_ldap_dn = 'ou=users,dc=ldap,dc=example,dc=com';
$plugin->settings->cas_ldap_filter = '(&(uid=:phpcas_username)(memberof=cn=isou,ou=groups,dc=ldap,dc=example,dc=com))';
$plugin->settings->cas_ldap_attribute_firstname = 'givenname';
$plugin->settings->cas_ldap_attribute_lastname = 'sn';
$plugin->settings->cas_ldap_attribute_email = 'mail';

$plugin->settings->cas_logout_redirection = '';
$plugin->settings->cas_verbose = false;

return $plugin;
