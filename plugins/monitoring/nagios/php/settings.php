<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/nagios/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['plugin_nagios_enable'], $options_yes_no[$_POST['plugin_nagios_enable']]) === true) {
    if ($plugin->active !== $_POST['plugin_nagios_enable']) {
        $plugin->active = $_POST['plugin_nagios_enable'];
        $plugin->save();

        if ($plugin->active === '1') {
            $_POST['successes'][] = 'Plugin Nagios activé.';
        } else {
            $_POST['successes'][] = 'Plugin Nagios désactivé.';
        }
    }
}

if (isset($_POST['plugin_nagios_path']) === true) {
    if ($plugin->settings->statusdat_path !== $_POST['plugin_nagios_path']) {
        $plugin->settings->statusdat_path = $_POST['plugin_nagios_path'];
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Chemin du status.dat enregistré.';
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$MONITORING_TEMPLATE = 'settings.tpl';
