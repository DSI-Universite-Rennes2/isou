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
 * Classe décrivant un service.
 */
class Service {
    /**
     * Identifiant de l'objet.
     *
     * @var integer
     */
    public $id;

    /**
     * Nom du service.
     *
     * @var string
     */
    public $name;

    /**
     * URL du service.
     *
     * @var string
     */
    public $url;

    /**
     * État en cours du service.
     *
     * @var integer
     */
    public $state;

    /**
     * Commentaire sur le service.
     *
     * @var string
     */
    public $comment;

    /**
     * Témoin indiquant si le service est activé. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $enable;

    /**
     * Témoin indiquant si le service est visible. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $visible;

    /**
     * Témoin indiquant si le service est verrouillé. Valeurs possibles '0' ou '1'.
     *
     * @var integer
     */
    public $locked;

    /**
     * Identifiant utilisé pour la construction du flux RSS.
     *
     * @var string
     */
    public $rsskey;

    /**
     * Date de dernière modification du service.
     *
     * @var \DateTime
     */
    public $timemodified;

    /**
     * Identifiant du module.
     *
     * @var integer
     */
    public $idplugin;

    /**
     * Identifiant de catégorie.
     *
     * @var integer
     */
    public $idcategory;

    /**
     * Nom de la catégorie.
     *
     * @var string
     */
    public $category;

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
            $this->url = null;
            $this->state = State::OK;
            $this->comment = null;
            $this->enable = '1';
            $this->visible = '1';
            $this->locked = '0';
            $this->rsskey = null;
            $this->timemodified = strftime('%FT%T');
            $this->idplugin = PLUGIN_ISOU;
            $this->idcategory = null;
            $this->category = '';
        }

        $this->is_closed = ($this->state === State::CLOSED);
        $this->is_forced = ($this->locked === '1');

        $this->dependencies = null;
        $this->reverse_dependencies = null;
    }

    /**
     * Représentation textuelle de la classe.
     *
     * @return string
     */
    public function __tostring() {
        return $this->name.' (id: '.$this->id.')';
    }

    /**
     * Contrôle les données avant de les enregistrer en base de données.
     *
     * @return string[] Retourne un tableau d'erreurs.
     */
    public function check_data() {
        $errors = array();

        $this->name = htmlentities(trim($this->name), ENT_NOQUOTES, 'UTF-8');
        if ($this->name === '') {
            $errors[] = 'Le nom du service ne peut pas être vide.';
        }

        $plugin = Plugin::get_record(array('id' => $this->idplugin, 'active' => true));
        if ($plugin === false) {
            $errors[] = 'Le type de service choisi est invalide.';
        }

        if (in_array($this->visible, array('0', '1'), $strict = true) === false) {
            $errors[] = 'La valeur choisie pour la visibilité n\'est pas valide.';
        }

        if (in_array($this->locked, array('0', '1'), $strict = true) === false) {
            $errors[] = 'La valeur choisie pour le verrouillage n\'est pas valide.';
        }

        if ($this->idplugin === PLUGIN_ISOU) {
            if ($this->url !== null) {
                $this->url = trim($this->url);
            }

            if ($this->url === '') {
                $this->url = null;
            }

            // TODO: créer une fonction set_rsskey().
            if ($this->rsskey === null) {
                global $DB;

                $sql = 'SELECT rsskey FROM services WHERE rsskey IS NOT NULL ORDER BY rsskey DESC';
                $query = $DB->query($sql);

                $key = $query->fetch(\PDO::FETCH_OBJ);
                if (isset($key->rsskey) === true) {
                    $this->rsskey = ++$key->rsskey;
                } else {
                    $this->rsskey = 1;
                    // $errors[] = 'La clé rss n\'a pu être générée.';
                }
            }

            $category = Category::get_record(array('id' => $this->idcategory));
            if ($category === false) {
                $errors[] = 'La catégorie choisie est invalide.';
            }
        } else {
            $this->url = null;
            $this->enable = '0';
            $this->visible = '0';
            $this->rsskey = null;
            $this->idcategory = null;
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
     * @return Service|false
     */
    public static function get_record(array $options = array()) {
        if (isset($options['id']) === false && isset($options['name'], $options['plugin']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] ou les paramètres $options[\'name\'] et $options[\'plugin\'] sont requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
     * Récupère un tableau d'objets en base de données en fonction des options passées en paramètre.
     *
     * Liste des options disponibles :
     *   category   => int
     *   enable     => bool
     *   id         => int
     *   locked     => bool
     *   fetch_one  => bool
     *   visible    => bool
     *   type       => int
     *
     * @param array $options Tableau d'options.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Service[]|Service|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $joins = array();
        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
                $conditions[] = 's.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['name']) === true) {
            if (is_string($options['name']) === true) {
                $conditions[] = 's.name = :name';
                $parameters[':name'] = $options['name'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'name\' doit être une chaîne de caractères. Valeur donnée : '.var_export($options['name'], $return = true));
            }

            unset($options['name']);
        }

        if (isset($options['enable']) === true) {
            if (is_bool($options['enable']) === true) {
                $conditions[] = 's.enable = :enable';
                $parameters[':enable'] = intval($options['enable']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'enable\' doit être un booléan. Valeur donnée : '.var_export($options['enable'], $return = true));
            }

            unset($options['enable']);
        }

        if (isset($options['locked']) === true) {
            if (is_bool($options['locked']) === true) {
                $conditions[] = 's.locked = :locked';
                $parameters[':locked'] = intval($options['locked']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'locked\' doit être un booléan. Valeur donnée : '.var_export($options['locked'], $return = true));
            }

            unset($options['locked']);
        }

        if (isset($options['state']) === true) {
            if (ctype_digit($options['state']) === true) {
                $conditions[] = 's.state = :state';
                $parameters[':state'] = $options['state'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'state\' doit être un entier. Valeur donnée : '.var_export($options['state'], $return = true));
            }

            unset($options['state']);
        }

        if (isset($options['visible']) === true) {
            if (is_bool($options['visible']) === true) {
                $conditions[] = 's.visible = :visible';
                $parameters[':visible'] = intval($options['visible']);
            } else {
                throw new \Exception(__METHOD__.': l\'option \'visible\' doit être un booléan. Valeur donnée : '.var_export($options['visible'], $return = true));
            }

            unset($options['visible']);
        }

        if (isset($options['category']) === true) {
            if (ctype_digit($options['category']) === true) {
                $conditions[] = 's.idcategory = :idcategory';
                $parameters[':idcategory'] = $options['category'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'category\' doit être un entier. Valeur donnée : '.var_export($options['category'], $return = true));
            }

            unset($options['category']);
        }

        if (isset($options['has_category']) === true) {
            if (is_bool($options['has_category']) === true) {
                if ($options['has_category'] === true) {
                    $joins[] = 'JOIN categories c ON c.id = s.idcategory';
                } else {
                    $conditions[] = 's.idcategory IS NULL';
                }
            } else {
                throw new \Exception(__METHOD__.': l\'option \'has_category\' doit être un booléan. Valeur donnée : '.var_export($options['has_category'], $return = true));
            }

            unset($options['has_category']);
        }

        if (isset($options['plugin']) === true) {
            if (ctype_digit($options['plugin']) === true) {
                $conditions[] = 's.idplugin = :plugin';
                $parameters[':plugin'] = $options['plugin'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'plugin\' doit être un entier. Valeur donnée : '.var_export($options['plugin'], $return = true));
            }

            unset($options['plugin']);
        }

        if (isset($options['dependencies_group']) === true) {
            if (ctype_digit($options['dependencies_group']) === true) {
                $joins[] = 'JOIN dependencies_groups_content dgc ON s.id = dgc.idservice';
                $conditions[] = 'dgc.idgroup = :dependencies_group';
                $parameters[':dependencies_group'] = $options['dependencies_group'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'dependencies_group\' doit être un entier. Valeur donnée : '.var_export($options['dependencies_group'], $return = true));
            }

            unset($options['dependencies_group']);
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
            $sql = 'SELECT s.id, s.name'.
                ' FROM services s'.
                ' JOIN plugins p ON p.id = s.idplugin AND p.active = 1'.
                ' '.implode(' ', $joins).
                $sql_conditions.
                ' ORDER BY UPPER(s.name)';

            $query = $DB->prepare($sql);
            $query->execute($parameters);

            return $query->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }

        $sql = 'SELECT s.id, s.name, s.url, s.state, s.comment, s.enable, s.visible, s.locked, s.rsskey, s.idplugin, s.idcategory'.
                ' FROM services s'.
                ' JOIN plugins p ON p.id = s.idplugin AND p.active = 1'.
                ' '.implode(' ', $joins).
                $sql_conditions.
                ' ORDER BY UPPER(s.name)';
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Service');

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

        $this->timemodified = strftime('%FT%T');

        $results = array(
            'successes' => array(),
            'errors' => array(),
        );

        $params = array(
            ':name' => $this->name,
            ':url' => $this->url,
            ':state' => $this->state,
            ':comment' => $this->comment,
            ':enable' => $this->enable,
            ':visible' => $this->visible,
            ':locked' => $this->locked,
            ':rsskey' => $this->rsskey,
            ':timemodified' => $this->timemodified,
            ':idplugin' => $this->idplugin,
            ':idcategory' => $this->idcategory,
        );

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO services(name, url, state, comment, enable, visible, locked, rsskey, timemodified, idplugin, idcategory)'.
                ' VALUES(:name, :url, :state, :comment, :enable, :visible, :locked, :rsskey, :timemodified, :idplugin, :idcategory)';
        } else {
            $sql = 'UPDATE services SET name=:name, url=:url, state=:state, comment=:comment, enable=:enable, visible=:visible, locked=:locked,'.
                ' rsskey=:rsskey, timemodified=:timemodified, idplugin=:idplugin, idcategory=:idcategory WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
            if (empty($this->id) === true) {
                $this->id = $DB->lastInsertId();
            }
            $results['successes'] = array('Les données ont été correctement enregistrées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

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

        $previous_transaction = $DB->inTransaction();
        if ($previous_transaction === false) {
            $DB->beginTransaction();
        }

        // Suppression des évènements, des dépendances et du service.
        $queries = array();
        $queries[] = 'DELETE FROM events WHERE idservice = :id';
        $queries[] = 'DELETE FROM dependencies_groups_content WHERE idservice = :id';
        $queries[] = 'DELETE FROM dependencies_groups WHERE idservice = :id';
        $queries[] = 'DELETE FROM services WHERE id = :id';
        foreach ($queries as $sql) {
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array(':id' => $this->id));
        }

        // Suppression des groupes sans contenu.
        $sql = 'DELETE FROM dependencies_groups WHERE id NOT IN (SELECT DISTINCT idgroup FROM dependencies_groups_content)';
        $query = $DB->prepare($sql);
        $commit &= $query->execute();

        if ($commit === 1) {
            if ($previous_transaction === false) {
                $DB->commit();
            }
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            if ($previous_transaction === false) {
                $DB->rollBack();
            }
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    /**
     * Change l'état du service.
     *
     * @param string $state Identifiant de l'état à attribuer.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors du changement d'état.
     *
     * @return Event|false Retourne l'évènement créé ou false si l'évènement a été fermé.
     */
    public function change_state(string $state) {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET state=:state, timemodified=:timemodified WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':state' => $state, ':timemodified' => strftime('%FT%T'), ':id' => $this->id)) === false) {
            throw new \Exception(implode(', ', $query->errorInfo()));
        }

        $this->state = $state;

        $event = $this->get_current_event();
        if ($event !== false && ($this->state === State::OK || $this->state !== $event->state)) {
            $event->close();
            $event = false;
        }

        if ($event === false && $this->state !== State::OK) {
            $event = new Event();
            $event->state = $this->state;
            $event->type = Event::TYPE_UNSCHEDULED;
            $event->idservice = $this->id;

            $event->save();
        }

        return $event;
    }

    /**
     * Active ou désactive le service.
     *
     * @param string $enable Témoin indiquant si le service doit être activé ou non. Valeurs possibles '0' ou '1'.
     *
     * @return boolean
     */
    public function enable(string $enable = '1') {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET state=:state, enable=:enable, timemodified=:timemodified WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':state' => State::OK, ':enable' => $enable, ':timemodified' => strftime('%FT%T'), ':id' => $this->id)) === true) {
            $this->enable = $enable;
            return true;
        } else {
            $LOGGER->error(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    /**
     * Désactive le service.
     *
     * @return boolean
     */
    public function disable() {
        return $this->enable('0');
    }

    /**
     * Affiche ou masque le service.
     *
     * @param string $visible Témoin indiquant si le service doit être affiché ou non. Valeurs possibles '0' ou '1'.
     *
     * @return boolean
     */
    public function visible(string $visible = '1') {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET visible=:visible WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':visible' => $visible, ':id' => $this->id)) === true) {
            $this->visible = '1';
            return true;
        } else {
            $LOGGER->error(implode(', ', $query->errorInfo()));
            return false;
        }
    }
    /**
     * Masque le service.
     *
     * @return boolean
     */
    public function hide() {
        return $this->visible('0');
    }

    /**
     * Indique si le service est verrouillé.
     *
     * @return boolean
     */
    public function is_locked() {
        return (empty($this->locked) === false);
    }

    /**
     * Verrouille le service et change l'état du service.
     *
     * @param string $state Identifiant de l'état à attribuer.
     *
     * @return boolean
     */
    public function lock(string $state) {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET state=:state, locked=1 WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':state' => $state, ':id' => $this->id)) === true) {
            $this->state = $state;
            $this->locked = '1';
            return true;
        } else {
            $LOGGER->error(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    /**
     * Déverrouille le service.
     *
     * @return boolean
     */
    public function unlock() {
        global $DB, $LOGGER;

        $sql = 'UPDATE services SET locked=0 WHERE id = :id';
        $query = $DB->prepare($sql);
        if ($query->execute(array(':id' => $this->id)) === true) {
            $this->locked = '0';
            return true;
        } else {
            $LOGGER->error(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    /**
     * Retourne toutes les dépendances.
     *
     * @return Dependency[]
     */
    public function get_dependencies() {
        if ($this->dependencies === null) {
            $this->set_dependencies();
        }

        return $this->dependencies;
    }

    /**
     * Définit les dépendances de ce service.
     *
     * @return void
     */
    public function set_dependencies() {
        $this->dependencies = Dependency_Group::get_records(array('service' => $this->id));
    }

    /**
     * Retourne toutes les dépendances inversées.
     *
     * @param string $state Identifiant de l'état.
     *
     * @return Dependency[]
     */
    public function get_reverse_dependencies(string $state = null) {
        if ($this->reverse_dependencies === null) {
            $this->reverse_dependencies = Dependency_Group::get_service_reverse_dependency_groups($this->id, $state);
        }

        return $this->reverse_dependencies;
    }

    /**
     * Définit les dépendances inversées.
     *
     * @param string $state Identifiant de l'état.
     *
     * @return void
     */
    public function set_reverse_dependencies(string $state = null) {
        $this->reverse_dependencies = $this->get_reverse_dependencies($state);
    }

    /**
     * Retourne tous les évènements de ce service.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @return Event[]
     */
    public function get_all_events(array $options = array()) {
        $options['idservice'] = $this->id;

        return Event::get_records($options);
    }

    /**
     * Retourne l'évènement en cours de ce service.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @return Event|false
     */
    public function get_current_event(array $options = array()) {
        $options['idservice'] = $this->id;
        $options['finished'] = false;
        $options['fetch_one'] = true;

        return Event::get_records($options);
    }

    /**
     * Retourne le (dernier ?) évènement clôturé de ce service.
     *
     * TODO: n'utilise pas l'option 'type' => Event::TYPE_CLOSED. À corriger.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @return Event[]
     */
    public function get_closed_event(array $options = array()) {
        $options['idservice'] = $this->id;
        $options['fetch_one'] = true;

        return Event::get_records($options);
    }

    /**
     * Retourne tous les évènements réguliers de ce service.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @return Event[]
     */
    public function get_regular_events(array $options = array()) {
        $options['idservice'] = $this->id;
        $options['regular'] = true;

        return Event::get_records($options);
    }
}
