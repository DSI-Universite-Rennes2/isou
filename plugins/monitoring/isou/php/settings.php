<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$smarty->addTemplateDir(PRIVATE_PATH.'/plugins/monitoring/isou/html');

$options_yes_no = array(
    1 => 'Oui',
    0 => 'Non',
);

if (isset($_POST['plugin_isou_tolerance']) === true) {
    if ((string) ($plugin->settings->tolerance / 60) !== $_POST['plugin_isou_tolerance'] && ctype_digit($_POST['plugin_isou_tolerance']) === true) {
        if ($_POST['plugin_isou_tolerance'] > 10) {
            $_POST['plugin_isou_tolerance'] = 10;
        } elseif ($_POST['plugin_isou_tolerance'] < 1) {
            $_POST['plugin_isou_tolerance'] = 1;
        }

        $plugin->settings->tolerance = $_POST['plugin_isou_tolerance'] * 60;
        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Tolérance d\'interruption enregistrée.';
    }
}

if (isset($_POST['plugin_isou_grouping']) === true) {
    if ($plugin->settings->grouping !== (bool) $_POST['plugin_isou_grouping'] && ctype_digit($_POST['plugin_isou_grouping']) === true) {
        $plugin->settings->grouping = (empty($_POST['plugin_isou_grouping']) === false);

        $plugin->update_settings($overwrite = true);

        $_POST['successes'][] = 'Paramétrage de groupement enregistré.';

        if ($plugin->settings->grouping === true) {
            require PRIVATE_PATH.'/plugins/monitoring/isou/lib.php';
            plugin_isou_update_grouping();
        }
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$smarty->assign('plugin', $plugin);

$MONITORING_TEMPLATE = 'settings.tpl';
