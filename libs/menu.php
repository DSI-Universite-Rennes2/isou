<?php

function get_menu() {
    global $DB;

    $menu = array();

    $sql = "SELECT id, label, title, url, active, model, position".
            " FROM menus".
            " ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    $query->setFetchMode(PDO::FETCH_CLASS, 'Isou\Helpers\Menu');

    while ($item = $query->fetch()) {
        $item->selected = false;
        $menu[$item->url] = $item;
    }

    return $menu;
}

function get_menu_sorted_by_url() {
    global $DB;

    $menu = array();

    $sql = "SELECT url, label".
            " FROM menus".
            " ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

function get_active_menu() {
    global $DB;

    $menu = array();

    $sql = "SELECT id, label, title, url, active, model, position".
            " FROM menus".
            " WHERE active=1".
            " ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    $query->setFetchMode(PDO::FETCH_CLASS, 'Isou\Helpers\Menu');

    while ($item = $query->fetch()) {
        $item->selected = false;
        $menu[$item->url] = $item;
    }

    return $menu;
}

function get_active_menu_sorted_by_url() {
    global $DB;

    $menu = array();

    $sql = "SELECT url, label".
            " FROM menus".
            " WHERE active=1".
            " ORDER BY position";
    $query = $DB->prepare($sql);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
}

function get_administration_menu() {
    $menu = array();

    $menu['evenements'] = (object) array(
        'url' => 'evenements',
        'title' => 'ajouter un évenement',
        'label' => 'évènements',
        'model' => '/php/events/index.php',
    );

    $menu['annonce'] = (object) array(
        'url' => 'annonce',
        'title' => 'ajouter une annonce',
        'label' => 'annonce',
        'model' => '/php/announcement/index.php',
    );

    $menu['statistiques'] = (object) array(
        'url' => 'statistiques',
        'title' => 'afficher les statistiques',
        'label' => 'statistiques',
        'model' => '/php/history/index.php',
    );

    $menu['services'] = (object) array(
        'url' => 'services',
        'title' => 'ajouter un service',
        'label' => 'services',
        'model' => '/php/services/index.php',
    );

    $menu['dependances'] = (object) array(
        'url' => 'dependances',
        'title' => 'ajouter une dépendance',
        'label' => 'dépendances',
        'model' => '/php/dependencies/index.php',
    );

    $menu['categories'] = (object) array(
        'url' => 'categories',
        'title' => 'ajouter une catégorie',
        'label' => 'catégories',
        'model' => '/php/categories/index.php',
    );

    $menu['configuration'] = (object) array(
        'url' => 'configuration',
        'title' => 'configurer l\'application',
        'label' => 'configuration',
        'model' => '/php/settings/index.php',
    );

    $menu['aide'] = (object) array(
        'url' => 'aide',
        'title' => 'afficher l\'aide',
        'label' => 'aide',
        'model' => '/php/help/index.php',
    );

    return $menu;
}
