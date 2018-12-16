<?php

use UniversiteRennes2\Isou\User;

$TITLE .= ' - Afficher les utilisateurs';

$users = User::get_records();

$smarty->assign('users', $users);

$SUBTEMPLATE = 'settings/users.tpl';
