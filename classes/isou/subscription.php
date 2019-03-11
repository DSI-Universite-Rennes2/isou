<?php

namespace UniversiteRennes2\Isou;

/**
 * Classe gérant les souscriptions aux notifications web.
 */
class Subscription {
    /**
     * Identifiant de la souscription.
     *
     * @var integer
     */
    public $id;

    /**
     * Adresse vers laquelle sera acheminé le message.
     *
     * @var string
     */
    public $endpoint;

    /**
     * Uncompressed public key P-256 encoded in Base64-URL.
     *
     * @var string
     */
    public $public_key;

    /**
     * Jeton d'authentification encodé en base64 de 24 caractères.
     *
     * @var string
     */
    public $authentification_token;

    /**
     * Algorythme d'encodage du message.
     *
     * @var string
     */
    public $content_encoding;

    /**
     * Date de la dernière notification.
     *
     * @var string
     */
    public $lastnotification;

    /**
     * Identifiant de l'utilisateur.
     *
     * @var integer
     */
    public $iduser;

    /**
     * Constructeur.
     *
     * @return void
     */
    public function __construct() {
        if (isset($this->id) === true) {
            if ($this->lastnotification !== null) {
                try {
                    $this->lastnotification = new DateTime($this->lastnotification);
                } catch (Exception $exception) {
                    $this->lastnotification = null;
                }
            }
        } else {
            $this->id = 0;
            $this->endpoint = '';
            $this->public_key = '';
            $this->authentification_token = '';
            $this->content_encoding = '';
            $this->lastnotification = null;
            $this->iduser = 0;
        }
    }

    /**
     * Supprime l'objet de la base de données.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors de l'écriture en base de données.
     *
     * @return void
     */
    public function delete() {
        global $DB, $LOGGER;

        $sql = 'DELETE FROM subscriptions WHERE id = :id';
        $query = $DB->prepare($sql);

        if ($query->execute(array(':id' => $this->id)) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de la souscription.');
        }
    }

    /**
     * @param array $options Array in format:
     *
     * @see function get_records()
     * Note : fetch_one param is always set at true
     *
     * @return UniversiteRennes2\Isou\Subscription|false
     */
    public static function get_record($options = array()) {
        $options['fetch_one'] = true;

        return self::get_records($options);
    }

    /**
     * Retourne un tableau des souscriptions en fonction des critères sélectionnés.
     *
     * @param array $options Liste des critères de sélection.
     *
     * @throws \Exception Lève une exception si certains critères minimum sont absents ou invalides.
     *
     * @return array of Subscription
     */
    public static function get_records($options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcours les options.
        if (isset($options['id']) === true) {
            if (ctype_digit($options['id']) === true) {
                $conditions[] = 's.id = :id';
                $parameters[':id'] = $options['id'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'id\' doit être un entier. Valeur donnée : '.var_export($options['id'], $return = true));
            }

            unset($options['id']);
        }

        if (isset($options['endpoint']) === true) {
            if (is_string($options['endpoint']) === true) {
                $conditions[] = 's.endpoint = :endpoint';
                $parameters[':endpoint'] = $options['endpoint'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'endpoint\' doit être un entier. Valeur donnée : '.var_export($options['endpoint'], $return = true));
            }

            unset($options['endpoint']);
        }

        if (isset($options['public_key']) === true) {
            if (is_string($options['public_key']) === true) {
                $conditions[] = 's.public_key = :public_key';
                $parameters[':public_key'] = $options['public_key'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'public_key\' doit être un entier. Valeur donnée : '.var_export($options['public_key'], $return = true));
            }

            unset($options['public_key']);
        }

        if (isset($options['authentification_token']) === true) {
            if (is_string($options['authentification_token']) === true) {
                $conditions[] = 's.authentification_token = :authentification_token';
                $parameters[':authentification_token'] = $options['authentification_token'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'authentification_token\' doit être un entier. Valeur donnée : '.var_export($options['authentification_token'], $return = true));
            }

            unset($options['authentification_token']);
        }

        if (isset($options['userid']) === true) {
            if (ctype_digit($options['userid']) === true) {
                $conditions[] = 's.userid = :userid';
                $parameters[':userid'] = $options['userid'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'userid\' doit être un entier. Valeur donnée : '.var_export($options['userid'], $return = true));
            }

            unset($options['userid']);
        }

        // Construis le WHERE.
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

        // Construis la requête.
        $sql = 'SELECT s.id, s.endpoint, s.public_key, s.authentification_token, s.content_encoding, s.lastnotification, s.iduser'.
                ' FROM subscriptions s'.
                ' '.$sql_conditions;
        $query = $DB->prepare($sql);
        $query->execute($parameters);

        $query->setFetchMode(\PDO::FETCH_CLASS, 'UniversiteRennes2\Isou\Subscription');

        if (isset($options['fetch_one']) === true) {
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    /**
     * Envoie une notification web.
     *
     * @param Webpush      $webpush      Objet webpush prêt à envoyer des notifications.
     * @param Notification $notification Object notification à envoyer contenant l'authentification, les entêtes, les options et le corps du message.
     *
     * @return true|array Retourne true en cas de succès, ou un tableau contenant les erreurs rencontrées à l'envoi du message.
     */
    public function notify($webpush, $notification) {
        return $webpush->sendNotification($this->endpoint, $notification->payload, $this->public_key, $this->authentification_token, $notification->flush, $notification->options);
    }

    /**
     * Enregistre l'objet en base de données.
     *
     * @throws \Exception Lève une exception en cas d'erreur lors de l'écriture en base de données.
     *
     * @return void
     */
    public function save() {
        global $DB, $LOGGER;

        $params = array(
            ':endpoint' => $this->endpoint,
            ':public_key' => $this->public_key,
            ':authentification_token' => $this->authentification_token,
            ':content_encoding' => $this->content_encoding,
            ':iduser' => $this->iduser,
        );

        if ($this->lastnotification instanceof \DateTime) {
            $params[':lastnotification'] = $this->lastnotification->format('Y-m-d\TH:i:s');
        } else {
            $params[':lastnotification'] = null;
        }

        if ($this->id === 0) {
            $sql = 'INSERT INTO subscriptions(endpoint, public_key, authentification_token, content_encoding, lastnotification, iduser)'.
                ' VALUES(:endpoint, :public_key, :authentification_token, :content_encoding, :lastnotification, :iduser)';
        } else {
            $sql = 'UPDATE subscriptions SET endpoint=:endpoint, public_key=:public_key, authentification_token=:authentification_token, content_encoding=:content_encoding,'.
                ' lastnotification=:lastnotification, iduser=:iduser WHERE id = :id';
            $params[':id'] = $this->id;
        }

        $query = $DB->prepare($sql);
        if ($query->execute($params) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->addError(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de la souscription.');
        }

        if ($this->id === 0) {
            $this->id = $DB->lastInsertId();
        }
    }
}
