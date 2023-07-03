<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$TITLE .= ' - Configuration d\'un proxy web';

if (isset($_POST['http_proxy'], $_POST['https_proxy'], $_POST['no_proxy']) === true) {
    // Enregistre les paramètres du proxy.
    if ($CFG['http_proxy'] !== $_POST['http_proxy'] && set_configuration('http_proxy', $_POST['http_proxy']) === true) {
        $CFG['http_proxy'] = $_POST['http_proxy'];
    }

    if ($CFG['https_proxy'] !== $_POST['https_proxy'] && set_configuration('https_proxy', $_POST['https_proxy']) === true) {
        $CFG['https_proxy'] = $_POST['https_proxy'];
    }

    $no_proxy = array();
    foreach (explode(',', $_POST['no_proxy']) as $value) {
        $value = trim($value);

        if (empty($value) === true) {
            continue;
        }

        $no_proxy[] = $value;
    }

    if ($no_proxy === array()) {
        if (empty($no_proxy) !== empty($CFG['no_proxy']) && set_configuration('no_proxy', '') === true) {
            $CFG['no_proxy'] = null;
        }
    } else {
        if ($no_proxy !== $CFG['no_proxy'] && set_configuration('no_proxy', json_encode($no_proxy)) === true) {
            $CFG['no_proxy'] = $no_proxy;
        }
    }
}

if (empty($CFG['no_proxy']) === true) {
    $no_proxy = '';
} else {
    $no_proxy = implode(', ', $CFG['no_proxy']);
}

$smarty->assign('no_proxy', $no_proxy);

$SUBTEMPLATE = 'settings/proxy.tpl';
