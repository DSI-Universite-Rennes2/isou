<?php

function get_event_description($id) {
    global $DB;

    $sql = "SELECT id, description, autogen".
            " FROM events_descriptions".
            " WHERE id=?";
    $query = $DB->prepare($sql);
    $query->execute(array($id));

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\EventDescription');

    return $query->fetch();
}


function get_event_description_by_content($description) {
    global $DB;

    $sql = "SELECT id, description, autogen".
            " FROM events_descriptions".
            " WHERE autogen=0".
            " AND description=?";
    $query = $DB->prepare($sql);
    $query->execute(array($description));

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\EventDescription');

    return $query->fetch();
}
