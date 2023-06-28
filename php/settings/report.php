<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

$TITLE .= ' - Configuration du rapport quotidien';

$options_yes_no = array(
    1 => 'yes',
    0 => 'no',
);

foreach (array('report_enabled', 'report_hour') as $key) {
    if (isset($_POST[$key]) === true && $_POST[$key] !== $CFG[$key]) {
        $value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
        if (set_configuration($key, $value) === true) {
            $CFG[$key] = $value;

            if ($key === 'report_enabled' && empty($value) === false) {
                set_configuration('last_daily_report', strftime('%FT%T', time() - 24 * 60 * 60));
            }
        }
    }
}

foreach (array('report_receiver', 'report_sender') as $key) {
    if (isset($_POST[$key]) === true && $_POST[$key] !== $CFG[$key]) {
        if (empty($_POST[$key]) === false && filter_var($_POST[$key], FILTER_VALIDATE_EMAIL) === false) {
            $_POST['errors'][] = $_POST[$key].' n\'est pas une adresse mail valide.';
            continue;
        }

        $value = $_POST[$key];
        if (set_configuration($key, $value) === true) {
            $CFG[$key] = $value;
        }
    }
}

$smarty->assign('options_yes_no', $options_yes_no);

$SUBTEMPLATE = 'settings/report.tpl';
