<?php

$TITLE .= ' - Configuration de l\'authentification';

$options_yes_no = array(
1 => 'yes',
0 => 'no',
);

$receivers = implode(PHP_EOL, $CFG['notification_receivers']);

foreach (array('notification_enabled', 'notification_hour', 'notification_receivers', 'notification_sender') as $key) {
    if (isset($_POST[$key]) && $_POST[$key] !== $CFG[$key]) {
        if ($key === 'notification_receivers') {
            $CFG['notification_receivers'] = array();
            foreach (explode(PHP_EOL, $_POST[$key]) as $user) {
                $user = trim($user);
                if (!empty($user)) {
                    $CFG['notification_receivers'][$user] = htmlentities($user, ENT_QUOTES, 'UTF-8');
                }
            }

            $CFG['notification_receivers'] = array_values($CFG['notification_receivers']);

            $value = json_encode($CFG['notification_receivers']);

            $receivers = implode(PHP_EOL, $CFG['notification_receivers']);
        } else {
            $value = htmlentities($_POST[$key], ENT_QUOTES, 'UTF-8');
        }

        if (set_configuration($key, $value)) {
            $CFG[$key] = $value;
        }
    }
}

$smarty->assign('receivers', $receivers);
$smarty->assign('options_yes_no', $options_yes_no);

$SUBTEMPLATE = 'settings/notifications.tpl';
