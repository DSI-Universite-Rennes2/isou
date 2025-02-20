<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/zabbix/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['plugin_zabbix_enable'], $options_yes_no[$_POST['plugin_zabbix_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_zabbix_enable']) {
        $plugin->active = $_POST['plugin_zabbix_enable'];
        $plugin->save();

        if ($plugin->active === '1') {
            $_POST['successes'][] = 'Plugin Zabbix activé.';
        } else {
            $_POST['successes'][] = 'Plugin Zabbix désactivé.';
        }
    }
}

if (isset($_POST['plugin_zabbix_url']) === true) {
    $_POST['plugin_zabbix_url'] = strtolower(trim($_POST['plugin_zabbix_url']));
    if ($plugin->settings->zabbix_url !== $_POST['plugin_zabbix_url']) {
        if (filter_var($_POST['plugin_zabbix_url'], FILTER_VALIDATE_URL) === false) {
            $_POST['errors'][] = 'URL non valide.';
        } elseif (in_array(parse_url($_POST['plugin_zabbix_url'], PHP_URL_SCHEME), array('http', 'https'), $strict = true) === false) {
            $_POST['errors'][] = 'Protocole de l\'URL non valide.';
        } else {
            $plugin->settings->zabbix_url = $_POST['plugin_zabbix_url'];
            $plugin->update_settings($overwrite = true);

            $_POST['successes'][] = 'URL Zabbix enregistrée.';
        }
    }
}

if (isset($_POST['plugin_zabbix_api_token']) === true) {
    if ($plugin->settings->zabbix_api_token !== $_POST['plugin_zabbix_api_token'] && $_POST['plugin_zabbix_api_token'] !== '* * * * *') {
        $plugin->settings->zabbix_api_token = $_POST['plugin_zabbix_api_token'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Clé d\'API Zabbix enregistrée.';
    }
}

if (isset($_POST['plugin_zabbix_tags']) === true) {
    if ($plugin->settings->zabbix_tags !== $_POST['plugin_zabbix_tags']) {
        $tags = array();
        foreach (explode(',', $_POST['plugin_zabbix_tags']) as $tagvalue) {
            $items = explode('=', $tagvalue);
            if (count($items) !== 2) {
                continue;
            }

            list($tag, $value) = $items;

            $tag = trim($tag);
            if (empty($tag) === true) {
                continue;
            }

            $value = trim($value);
            if (empty($value) === true) {
                continue;
            }

            $tags[] = sprintf('%s=%s', $tag, $value);
        }

        $_POST['plugin_zabbix_tags'] = implode(',', $tags);
        $plugin->settings->zabbix_tags = $_POST['plugin_zabbix_tags'];

        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Tags Zabbix enregistrés.';
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$MONITORING_TEMPLATE = 'settings.tpl';
