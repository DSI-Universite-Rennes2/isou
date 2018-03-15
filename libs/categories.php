<?php

function get_category($id) {
    global $DB;

    $sql = "SELECT id, name, position FROM categories WHERE id=?";
    $query = $DB->prepare($sql);
    $query->execute(array($id));

    $query->setFetchMode(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Category');

    return $query->fetch();
}

function get_categories() {
    global $DB;

    $sql = "SELECT id, name, position FROM categories ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Category');
}

function get_categories_sorted_by_id() {
    global $DB;

    $sql = "SELECT id, name FROM categories ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}
