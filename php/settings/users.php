<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\User;

$TITLE .= ' - Afficher les utilisateurs';

$users = User::get_records();

$smarty->assign('users', $users);

$SUBTEMPLATE = 'settings/users.tpl';
