<?php

namespace UniversiteRennes2\Isou;

class Category{
    public $id;
    public $name;
    public $position;
    public $services;

    public function __construct() {
        if (!isset($this->id)) {
            // instance manuelle
            $this->id = 0;
            $this->name = '';
            $this->position = null;
        }

        $services = null;
    }

    public function get_services() {
        if ($this->services === null) {
            require_once PRIVATE_PATH.'/libs/services.php';

            $this->services = get_services(array('category' => $this->id));
        }

        return $this->services;
    }

    public function check_data() {
        $errors = array();

        if (empty($this->name)) {
            $errors[] = 'Le nom de la catégorie ne peut pas être vide.';
        } else {
            $this->name = htmlentities($this->name, ENT_QUOTES, 'UTF-8');
        }

        if ($this->position === null) {
            $this->position = (string) (count(get_categories()) + 1);
        } elseif (ctype_digit($this->position) === false) {
            $errors[] = 'La position de la catégorie doit être un entier.';
        }

        return $errors;
    }


    public function save() {
        global $DB, $LOGGER;

        $results = array(
        'successes' => array(),
        'errors' => array(),
        );
        $params = array(
        $this->name,
        $this->position,
        );

        if ($this->id === 0) {
            $sql = "INSERT INTO categories(name, position) VALUES(?,?)";
        } else {
            $sql = "UPDATE categories SET name=?, position=? WHERE idcategory=?";
            $params[] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params)) {
            if ($this->id === 0) {
                $this->id = $DB->lastInsertId();
            }
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
        }

        return $results;
    }

    public function delete() {
        global $DB, $LOGGER;

        require_once PRIVATE_PATH.'/libs/services.php';

        $results = array(
        'successes' => array(),
        'errors' => array(),
        );
        $commit = 1;

        $DB->beginTransaction();

        $services = get_services(array('category' => $this->id));
        foreach ($services as $service) {
            $results = array_merge($results, $service->delete());
            $commit &= !isset($results['errors'][0]);
            if ($commit === 0) {
                break;
            }
        }

        if ($commit === 1) {
            $sql = "DELETE FROM categories WHERE idcategory=?";
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array($this->id));

            $sql = "UPDATE categories SET position=position-1 WHERE position > ?";
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array($this->position));
        }

        if ($commit === 1) {
            $DB->commit();
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $DB->rollBack();
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    public function up() {
        global $DB, $LOGGER;

        $results = array(
        'successes' => array(),
        'errors' => array(),
        );

        if ($this->position == 1) {
            $results['errors'][] = 'La catégorie "'.$this->name.'" ne peut pas être montée davantage.';
        } else {
            $commit = 1;
            $DB->beginTransaction();

            $sql = "UPDATE categories SET position=position-1 WHERE idcategory = ?";
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array($this->id));

            if ($commit === 1) {
                $sql = "UPDATE categories SET position=position+1 WHERE idcategory != ? AND position = ?";
                $query = $DB->prepare($sql);
                $commit &= $query->execute(array($this->id, $this->position - 1));
            }

            if ($commit === 1) {
                $DB->commit();
                $this->position = $this->position - 1;
                $results['successes'] = array('Les données ont été correctement enregistrées.');
            } else {
                // log db errors
                $LOGGER->addError(implode(', ', $query->errorInfo()));

                $DB->rollBack();
                $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
            }
        }

        return $results;
    }

    public function down($limit = null) {
        global $DB, $LOGGER;

        $results = array(
        'successes' => array(),
        'errors' => array(),
        );

        if ($limit === null) {
            $limit = count(get_categories());
        }

        if ($this->position == $limit) {
            $results['errors'][] = 'La catégorie "'.$this->name.'" ne peut pas être descendue davantage.';
        } else {
            $commit = 1;
            $DB->beginTransaction();

            $sql = "UPDATE categories SET position=position+1 WHERE idcategory = ?";
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array($this->id));

            if ($commit === 1) {
                $sql = "UPDATE categories SET position=position-1 WHERE idcategory != ? AND position = ?";
                $query = $DB->prepare($sql);
                $commit &= $query->execute(array($this->id, $this->position + 1));
            }

            if ($commit === 1) {
                $DB->commit();
                $this->position = $this->position + 1;
                $results['successes'] = array('Les données ont été correctement enregistrées.');
            } else {
                // log db errors
                $LOGGER->addError(implode(', ', $query->errorInfo()));

                $DB->rollBack();
                $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
            }
        }

        return $results;
    }
}
