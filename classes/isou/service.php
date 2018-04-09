<?php

namespace UniversiteRennes2\Isou;

class Service{
    public $id;
    public $name;
    public $url;
    public $state;
    public $comment;
    public $enable;
    public $visible;
    public $locked;
    public $rsskey;
    public $timemodified;
    public $idplugin;
    public $idcategory;
    public $category;

    public function __construct() {
        if (isset($this->id) === false) {
            // Manual instance.
            $this->id = 0;
            $this->name = '';
            $this->url = null;
            $this->state = 0;
            $this->comment = null;
            $this->enable = 1;
            $this->visible = 1;
            $this->locked = 0;
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

    function check_data($categories = array()) {
        $errors = array();

        $this->name = htmlentities($this->name, ENT_NOQUOTES, 'UTF-8');
        if ($this->name === '') {
            $errors[] = 'Le nom du service ne peut pas être vide.';
        }

        $plugin = Plugin::get_plugins(array('single' => true, 'id' => $this->idplugin, 'active' => true));
        if ($plugin === false) {
            $errors[] = 'Le type de service choisi est invalide.';
        }

        if (!in_array($this->visible, array(0, 1))) {
            $errors[] = 'La visibilité choisi est invalide.';
        }

        if ($this->idplugin === PLUGIN_ISOU) {
            if ($this->url === '') {
                $this->url = null;
            }

            if ($this->rsskey === null) {
                global $DB;

                $sql = "SELECT rsskey FROM services WHERE rsskey IS NOT NULL ORDER BY rsskey DESC";
                $query = $DB->query($sql);
                if ($key = $query->fetch(\PDO::FETCH_OBJ)) {
                    $this->rsskey = ++$key->rsskey;
                } else {
                    $this->rsskey = 1;
                    // $errors[] = 'La clé rss n\'a pu être générée.';
                }
            }

            if (!isset($categories[$this->idcategory])) {
                $errors[] = 'La catégorie choisie est invalide.';
            }
        } else {
            $this->url = null;
            $this->enable = 0;
            $this->visible = 0;
            $this->rsskey = null;
            $this->idcategory = null;
        }

        return $errors;
    }

    function save() {
        global $DB, $LOGGER;

        $this->timemodified = strftime('%FT%T');
        $results = array(
            'successes' => array(),
            'errors' => array(),
            );
        $params = array(
            $this->name,
            $this->url,
            $this->state,
            $this->comment,
            $this->enable,
            $this->visible,
            $this->locked,
            $this->rsskey,
            $this->timemodified,
            $this->idplugin,
            $this->idcategory,
            );

        if ($this->id === 0) {
            $sql = "INSERT INTO services(name, url, state, comment, enable, visible, locked, rsskey, timemodified, idplugin, idcategory) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        } else {
            $sql = "UPDATE services SET name=?, url=?, state=?, comment=?, enable=?, visible=?, locked=?, rsskey=?, timemodified=?, idplugin=?, idcategory=? WHERE id=?";
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

    function delete() {
        global $DB, $LOGGER;

        $results = array(
            'successes' => array(),
            'errors' => array(),
            );
        $commit = 1;

        $previous_transaction = $DB->inTransaction();
        if (!$previous_transaction) {
            $DB->beginTransaction();
        }

        // suppression des évènements, des dépendances et du service
        $queries = array();
        $queries[] = "DELETE FROM events WHERE idservice=?";
        $queries[] = "DELETE FROM dependencies_groups_content WHERE idservice=?";
        $queries[] = "DELETE FROM dependencies_groups WHERE idservice=?";
        $queries[] = "DELETE FROM services WHERE id=?";
        foreach ($queries as $sql) {
            $query = $DB->prepare($sql);
            $commit &= $query->execute(array($this->id));
        }

        // suppression des groupes sans contenu
        $sql = "DELETE FROM dependencies_groups WHERE id NOT IN (SELECT DISTINCT idgroup FROM dependencies_groups_content)";
        $query = $DB->prepare($sql);
        $commit &= $query->execute();

        if ($commit === 1) {
            if (!$previous_transaction) {
                $DB->commit();
            }
            $results['successes'] = array('Les données ont été correctement supprimées.');
        } else {
            // log db errors
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            if (!$previous_transaction) {
                $DB->rollBack();
            }
            $results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
        }

        return $results;
    }

    public function change_state($state) {
        global $DB, $LOGGER;

        $sql = "UPDATE services SET state=?, timemodified=? WHERE id=?";
        $query = $DB->prepare($sql);
        if ($query->execute(array($state, strftime('%FT%T'), $this->id))) {
            // $LOGGER->addInfo('Le service "'.$this->name.'" est passé de l\'état '.$this->state.' à l\'état '.$state.'.');
            $this->state = $state;
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function enable($enable = '1') {
        global $DB, $LOGGER;

        $sql = "UPDATE services SET state=?, enable=?, timemodified=? WHERE id=?";
        $query = $DB->prepare($sql);
        if ($query->execute(array(State::OK, $enable, strftime('%FT%T'), $this->id))) {
            $this->enable = $enable;
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function disable() {
        return $this->enable('0');
    }

    public function visible($visible = '1') {
        global $DB, $LOGGER;

        $sql = "UPDATE services SET visible=? WHERE id=?";
        $query = $DB->prepare($sql);
        if ($query->execute(array($visible, $this->id))) {
            $this->visible = '1';
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function hide() {
        return $this->visible('0');
    }

    public function lock($state) {
        global $DB, $LOGGER;

        $sql = "UPDATE services SET state=?, locked=1 WHERE id = ?";
        $query = $DB->prepare($sql);
        if ($query->execute(array($state, $this->id))) {
            $this->state = $state;
            $this->locked = '1';
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function unlock() {
        global $DB, $LOGGER;

        $sql = "UPDATE services SET locked=0 WHERE id = ?";
        $query = $DB->prepare($sql);
        if ($query->execute(array($this->id))) {
            $this->locked = '0';
            return true;
        } else {
            $LOGGER->addError(implode(', ', $query->errorInfo()));
            return false;
        }
    }

    public function get_dependencies() {
        if ($this->dependencies === null) {
            require_once PRIVATE_PATH.'/libs/dependencies.php';

            $this->dependencies = get_dependency_groups(array('service' => $this->id));
        }

        return $this->dependencies;
    }

    public function get_reverse_dependencies($state = null) {
        if ($this->reverse_dependencies === null) {
            require_once PRIVATE_PATH.'/libs/dependencies.php';

            $this->reverse_dependencies = get_service_reverse_dependency_groups($this->id, $state);
        }

        return $this->reverse_dependencies;
    }

    public function set_reverse_dependencies($state = null) {
        $this->reverse_dependencies = $this->get_reverse_dependencies($state);
    }

    public function get_next_scheduled_events($options = array()) {
        $options['idservice'] = $this->id;
        $options['type'] = Event::TYPE_SCHEDULED;
        $options['after'] = new \DateTime();
        $options['regular'] = false;

        return get_events($options);
    }

    public function get_last_events($options = array()) {
        $options['idservice'] = $this->id;
        $options['before'] = new \DateTime();

        return get_events($options);
    }

    public function get_all_events($options = array()) {
        $options['idservice'] = $this->id;

        return get_events($options);
    }

    public function get_current_event($options = array()) {
        $options['idservice'] = $this->id;
        $options['finished'] = false;
        $options['one_record'] = true;

        return get_events($options);
    }

    public function get_closed_event($options = array()) {
        $options['idservice'] = $this->id;
        $options['one_record'] = true;

        return get_events($options);
    }

    public function get_regular_events($options = array()) {
        $options['idservice'] = $this->id;
        $options['regular'] = true;

        return get_events($options);
    }

    public function __toString() {
        return $this->name.' (id: '.$this->id.')';
    }

    public function __destruct() {
        // object destructed
    }
}
