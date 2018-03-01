<?php

$TITLE .= ' - Affichage d\'informations';

$HTMLPurifier = new HTMLPurifier();
$smarty->assign('HTMLPurifierVersion', $HTMLPurifier->version);

$smarty->assign('phpCASVersion', phpCAS::getVersion());

$SUBTEMPLATE = 'configuration/information.tpl';

