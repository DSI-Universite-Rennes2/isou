<?php

$TITLE .= ' - Contact';

$sql = "SELECT message FROM contact";
$query = $DB->prepare($sql);
$query->execute();
$contact = $query->fetch(PDO::FETCH_OBJ);

$is_admin = (isset($USER->admin) && empty($USER->admin) === false);

if ($is_admin === true && isset($PAGE_NAME[1]) === true && $PAGE_NAME[1] === 'edit') {
    $_GET['edit'] = true;
    if (isset($_POST['message']) === true) {
        $html_purifier = new HTMLPurifier();
        $contact->message = $html_purifier->purify($_POST['message']);

        $sql = "UPDATE contact SET message=?";
        $query = $DB->prepare($sql);
        if ($query->execute(array($contact->message)) === true) {
            $_SESSION['messages'] = array('successes' => 'Page enregistrée.');

            header('Location: '.URL.'/index.php/contact');
            exit(0);
        } else {
            $_POST['errors'] = array('Page non enregistrée.');
        }
    }
}

$smarty->assign('message', $contact->message);

$TEMPLATE = 'public/contact.tpl';
