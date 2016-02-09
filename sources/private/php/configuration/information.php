<?php

$TITLE = NAME.' - Affichage d\'informations';

if(!defined('HTMLPurifier')){
	require PRIVATE_PATH.'/classes/htmlpurifier/library/HTMLPurifier.auto.php';
}
$HTMLPurifier = new HTMLPurifier();
$smarty->assign('HTMLPurifierVersion', $HTMLPurifier->version);

if(!class_exists('phpCAS')){
	require PRIVATE_PATH.'/classes/phpCAS/CAS.php';
}
$smarty->assign('phpCASVersion', phpCAS::getVersion());

$SUBTEMPLATE = 'configuration/information.tpl';

