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
 * Classe gérant les utilisateurs.
 */
class User {
    /**
     * Identifiant de l'utilisateur.
     *
     * @var integer
     */
    public $id;

    /**
     * Nom du module d'authentification.
     *
     * @var string
     */
    public $authentification;

    /**
     * Identifiant de connexion.
     *
     * @var string
     */
    public $username;

    /**
     * Mot de passe haché.
     *
     * @var string
     */
    public $password;

    /**
     * Prénom de l'utilisateur.
     *
     * @var string
     */
    public $firstname;

    /**
     * Nom de l'utilisateur.
     *
     * @var string
     */
    public $lastname;

    /**
     * Email de l'utilisateur.
     *
     * @var string
     */
    public $email;

    /**
     * Témoin du droit d'administration.
     *
     * @var integer
     */
    public $admin;

    /**
     * Date de dernière accès à Isou.
     *
     * @var \DateTime
     */
    public $lastaccess;

    /**
     * Date de création du compte.
     *
     * @var \DateTime
     */
    public $timecreated;

    /**
     * Constructeur.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->id) === true) {
            // Instance PDO.
            try {
                $this->timecreated = new \DateTime($this->timecreated);
            } catch (\Exception $exception) {
                $this->timecreated = new \DateTime();
            }

            if ($this->lastaccess !== null) {
                try {
                    $this->lastaccess = new \DateTime($this->lastaccess);
                } catch (\Exception $exception) {
                    $this->lastaccess = null;
                }
            }
        } else {
            // Instance manuelle.
            $this->id = '0';
            $this->authentification = 'manual';
            $this->username = 'anonymous';
            $this->password = '';
            $this->firstname = '';
            $this->lastname = '';
            $this->email = '';
            $this->admin = '0';
            $this->lastaccess = null;
            $this->timecreated = new \DateTime();
        }
    }

    /**
     * Retourne si renseigné le prénom et le nom de l'utilisateur, sinon son identifiant.
     *
     * @return string
     */
    public function __tostring() {
        if (empty($this->firstname) === true && empty($this->lastname) === true) {
            return $this->username;
        }

        return trim($this->firstname.' '.$this->lastname);
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return User|false
     */
    public static function get_record(array $options = array()) {
        if (isset($options['id']) === false && isset($options['username'], $options['authentification']) === false) {
            throw new \Exception(__METHOD__.': le paramètre $options[\'id\'] ou les deux paramètres $options[\'username\'] et $options[\'authentification\'] sont requis.');
        }

        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
     * Récupère un tableau d'objets en base de données en fonction des options passées en paramètre.
     *
     * Liste des options disponibles : TODO.
     *
     * @param array $options Liste des critères de sélection.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return User[]|User|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
                $conditions[] = 'u.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['username']) === true) {
            if (is_string($options['username']) === true) {
                $conditions[] = 'u.username = :username';
                $parameters[':username'] = $options['username'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'username\' doit être une chaine de caractères. Valeur donnée : '.var_export($options['username'], $return = true));
            }

            unset($options['username']);
        }

        if (isset($options['authentification']) === true) {
            if (is_string($options['authentification']) === true) {
                $conditions[] = 'u.authentification = :authentification';
                $parameters[':authentification'] = $options['authentification'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'authentification\' doit être une chaine de caractères. Valeur donnée : '.var_export($options['authentification'], $return = true));
            }

            unset($options['authentification']);
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
        $sql = 'SELECT u.id, u.authentification, u.username, u.password, u.firstname, u.lastname, u.email, u.admin, u.lastaccess, u.timecreated'.
           ' FROM users u'.
            $sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\User');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Enregistre ou mets à jour un utilisateur en base de données.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors de l'écriture en base de données.
     *
     * @return void
     */
    public function save() {
        global $DB, $LOGGER;

        if ($this->lastaccess === null) {
            $lastaccess = null;
        } else {
            $lastaccess = $this->lastaccess->format('Y-m-d\TH:i:s');
        }

        $params = array(
            ':authentification' => $this->authentification,
            ':username' => $this->username,
            ':password' => $this->password,
            ':firstname' => $this->firstname,
            ':lastname' => $this->lastname,
            ':email' => $this->email,
            ':admin' => $this->admin,
            ':lastaccess' => $lastaccess,
            ':timecreated' => $this->timecreated->format('Y-m-d\TH:i:s'),
        );

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO users(authentification, username, password, firstname, lastname, email, admin, lastaccess, timecreated)'.
                ' VALUES(:authentification, :username, :password, :firstname, :lastname, :email, :admin, :lastaccess, :timecreated)';
        } else {
            $sql = 'UPDATE users SET authentification=:authentification, username=:username, password=:password, firstname=:firstname, lastname=:lastname,'.
                ' email=:email, admin=:admin, lastaccess=:lastaccess, timecreated=:timecreated WHERE id = :id';
            $params[':id'] = $this->id;
        }
        $query = $DB->prepare($sql);

        if ($query->execute($params) === true) {
            if (empty($this->id) === true) {
                $this->id = $DB->lastInsertId();
            }
        } else {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de l\'utilisateur.');
        }
    }
}
