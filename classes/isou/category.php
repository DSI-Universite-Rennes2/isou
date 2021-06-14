<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

/**
 * Classe décrivant une catégorie.
 */
class Category {
    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Nom de la catégorie.
     *
     * @var string
     */
    public $name;

    /**
     * Position de la catégorie.
     *
     * @var integer
     */
    public $position;

    /**
     * Tableau des services associés à cette catégorie.
     *
     * @var Service[]
     */
    public $services;

    /**
     * Constructeur de la classe.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->id) === false) {
            // Instance manuelle.
            $this->id = '0';
            $this->name = '';
            $this->position = null;
        }

        $services = null;
    }

    /**
     * Retourne tous les services associés à cette catégorie.
     *
     * @return Service[]
     */
    public function get_services() {
        if ($this->services === null) {
            $this->services = Service::get_records(array('category' => $this->id));
        }

        return $this->services;
    }

    /**
     * Contrôle les données avant de les enregistrer en base de données.
     *
     * @return string[] Retourne un tableau d'erreurs.
     */
    public function check_data() {
        $errors = array();

        if (empty($this->name) === true) {
            $errors[] = 'Le nom de la catégorie ne peut pas être vide.';
        } else {
            $this->name = htmlentities($this->name, ENT_QUOTES, 'UTF-8');
        }

        if ($this->position === null) {
            $this->position = (string) (count(self::get_records()) + 1);
        } elseif (ctype_digit($this->position) === false) {
            $errors[] = 'La position de la catégorie doit être un entier.';
        }

        return $errors;
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Category|false
     */
    public static function get_record(array $options = array()) {
        if (isset($options['id']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] est requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
     * Récupère un tableau d'objets en base de données en fonction des options passées en paramètre.
     *
     * Liste des options disponibles : TODO.
     *
     * @param array $options Tableau d'options.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Category[]
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $joins = array();
        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
                $conditions[] = 'c.id = ?';
                $parameters[] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['only-visible-services']) === true) {
            if (isset($options['non-empty']) === false) {
                throw new \Exception(__METHOD__.': pour utiliser l\'option \'only-visible-services\, l\'option \'non-empty\' est requise.');
            } elseif (is_bool($options['only-visible-services']) === false) {
                throw new \Exception(__METHOD__.': l\'option \'only-visible-services\' doit être un booléan. Valeur donnée : '.var_export($options['non-empty'], $return = true));
            } elseif ($options['only-visible-services'] === true) {
                $conditions[] = 's.visible = 1';
            }

            unset($options['only-visible-services']);
        }

        if (isset($options['non-empty']) === true) {
            if (is_bool($options['non-empty']) === false) {
                throw new \Exception(__METHOD__.': l\'option \'non-empty\' doit être un booléan. Valeur donnée : '.var_export($options['non-empty'], $return = true));
            } elseif ($options['non-empty'] === true) {
                $joins[] = ' JOIN services s ON c.id = s.idcategory';
            }

            unset($options['non-empty']);
        }

        // Construit le WHERE.
        if (isset($conditions[0]) === true) {
            $sql_conditions = ' WHERE '.implode(' AND ', $conditions);
        } else {
            $sql_conditions = '';
        }

        // Vérifie si toutes les options ont été utilisées.
        foreach ($options as $key => $option) {
            if (in_array($key, array('fetch_column', 'fetch_one'), $strict = true) === true) {
                continue;
            }

            throw new \Exception(__METHOD__.': l\'option \''.$key.'\' n\'a pas été utilisée. Valeur donnée : '.var_export($option, $return = true));
        }

        // Construit la requête.
        if (isset($options['fetch_column']) === true) {
            $sql = 'SELECT c.id, c.name'.
                ' FROM categories c'.
                implode(' ', $joins).
                $sql_conditions.
                ' ORDER BY c.position';

            $query = $DB->prepare($sql);
            $query->execute($parameters);

            return $query->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }

        $sql = 'SELECT c.id, c.name, c.position'.
            ' FROM categories c'.
            implode(' ', $joins).
            $sql_conditions.
            ' ORDER BY c.position';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Category');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Enregistre l'objet en base de données.
     *
     * @return array
     */
    public function save() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        $parameters = array(
            ':name' => $this->name,
            ':position' => $this->position,
        );

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO categories(name, position) VALUES(:name, :position)';
        } else {
            $sql = 'UPDATE categories SET name=:name, position=:position WHERE id=:id';
            $parameters[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($parameters) === true) {
            if (empty($this->id) === true) {
                $this->id = $DB->lastInsertId();
            }

            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
        }

        return $results;
    }

    /**
     * Supprime l'objet en base de données.
     *
     * @return array
     */
    public function delete() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );
        $commit = 1;

        $DB->beginTransaction();

        $services = Service::get_records(array('category' => $this->id));
        foreach ($services as $service) {
            $results = array_merge($results, $service->delete());
            $commit &= (isset($results['errors'][0]) === false);
            if ($commit === 0) {
                break;
            }
        }

        if ($commit === 1) {
            $sql = 'DELETE FROM categories WHERE id = :id';
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':id' => $this->id));

            $sql = 'UPDATE categories SET position=position-1 WHERE position > :position';
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':position' => $this->position));
        }

        if ($commit === 1) {
            $DB->commit();
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            $DB->rollBack();
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    /**
     * Monte la catégorie d'une position.
     *
     * @return array
     */
    public function move_up() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        if ((int) $this->position === 1) {
            $results['errors'][] = 'La catégorie "'.$this->name.'" ne peut pas être montée davantage.';
        } else {
            $commit = 1;
            $DB->beginTransaction();

            $sql = 'UPDATE categories SET position=position-1 WHERE id = :id';
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':id' => $this->id));

            if ($commit === 1) {
                $sql = 'UPDATE categories SET position=position+1 WHERE id != :id AND position = :position';
                $query = $DB->prepare($sql);
                $commit &= $query->execute(array(':id' => $this->id, ':position' => $this->position - 1));
            }

            if ($commit === 1) {
                $DB->commit();
                $this->position = $this->position - 1;
                $results['successes'] = array('Les données ont été correctement enregistrées.');
            } else {
                // Enregistre le message d'erreur.
                $LOGGER->addError(implode(', ', $query->errorInfo()));

                $DB->rollBack();
                $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
            }
        }

        return $results;
    }

    /**
     * Descend la catégorie d'une position.
     *
     * @param integer|null $limit Nombre maximum de catégories. Si NULL, ce nombre est calculé.
     *
     * @return array
     */
    public function move_down(int $limit = null) {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        if ($limit === null) {
            $limit = count(self::get_records());
        }

        if ((int) $this->position === $limit) {
            $results['errors'][] = 'La catégorie "'.$this->name.'" ne peut pas être descendue davantage.';
        } else {
            $commit = 1;
            $DB->beginTransaction();

            $sql = 'UPDATE categories SET position=position+1 WHERE id = :id';
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':id' => $this->id));

            if ($commit === 1) {
                $sql = 'UPDATE categories SET position=position-1 WHERE id != :id AND position = :position';
                $query = $DB->prepare($sql);
                $commit &= $query->execute(array(':id' => $this->id, ':position' => $this->position + 1));
            }

            if ($commit === 1) {
                $DB->commit();
                $this->position = $this->position + 1;
                $results['successes'] = array('Les données ont été correctement enregistrées.');
            } else {
                // Enregistre le message d'erreur.
                $LOGGER->addError(implode(', ', $query->errorInfo()));

                $DB->rollBack();
                $results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
            }
        }

        return $results;
    }
}
