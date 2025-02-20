<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

use DateTime;
use Exception;
use Minishlink\WebPush\Subscription as SubscriptionInterface;
use Minishlink\WebPush\WebPush;

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
    public $authentication_token;

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
            $this->id = '0';
            $this->endpoint = '';
            $this->public_key = '';
            $this->authentication_token = '';
            $this->content_encoding = '';
            $this->lastnotification = null;
            $this->iduser = '0';
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
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de la suppression de la souscription.');
        }
    }

    /**
     * Récupère un objet en base de données en fonction des options passées en paramètre.
     *
     * @param array $options Tableau d'options. @see get_records.
     *
     * @throws \Exception Lève une exception lorsqu'une option n'est pas valide.
     *
     * @return Subscription|false
     */
    public static function get_record(array $options = array()) {
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
     * @return Subscription[]|Subscription|false
     */
    public static function get_records(array $options = array()) {
        global $DB;

        $conditions = array();
        $parameters = array();

        // Parcourt les options.
        if (isset($options['id']) === true) {
            if (is_string($options['id']) === true && ctype_digit($options['id']) === true) {
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

        if (isset($options['authentication_token']) === true) {
            if (is_string($options['authentication_token']) === true) {
                $conditions[] = 's.authentication_token = :authentication_token';
                $parameters[':authentication_token'] = $options['authentication_token'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'authentication_token\' doit être un entier. Valeur donnée : '.var_export($options['authentication_token'], $return = true));
            }

            unset($options['authentication_token']);
        }

        if (isset($options['userid']) === true) {
            if (is_string($options['userid']) === true && ctype_digit($options['userid']) === true) {
                $conditions[] = 's.userid = :userid';
                $parameters[':userid'] = $options['userid'];
            } else {
                throw new \Exception(__METHOD__.': l\'option \'userid\' doit être un entier. Valeur donnée : '.var_export($options['userid'], $return = true));
            }

            unset($options['userid']);
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
        $sql = 'SELECT s.id, s.endpoint, s.public_key, s.authentication_token, s.content_encoding, s.lastnotification, s.iduser'.
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
     * @param WebPush $webpush Objet webpush prêt à envoyer des notifications.
     * @param Notification $notification Object notification à envoyer contenant l'authentification, les entêtes, les options et le corps du message.
     *
     * @return \Minishlink\WebPush\MessageSentReport
     */
    public function notify(WebPush $webpush, Notification $notification) {
        $parameters = array();
        $parameters['endpoint'] = $this->endpoint;
        $parameters['publicKey'] = $this->public_key;
        $parameters['authToken'] = $this->authentication_token;
        $parameters['contentEncoding'] = $this->content_encoding;
        $subscription = SubscriptionInterface::create($parameters);

        return $webpush->sendOneNotification($subscription, $notification->payload, $notification->options);
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
            ':authentication_token' => $this->authentication_token,
            ':content_encoding' => $this->content_encoding,
            ':iduser' => $this->iduser,
        );

        if ($this->lastnotification instanceof \DateTime) {
            $params[':lastnotification'] = $this->lastnotification->format('Y-m-d\TH:i:s');
        } else {
            $params[':lastnotification'] = null;
        }

        if (empty($this->id) === true) {
            $sql = 'INSERT INTO subscriptions(endpoint, public_key, authentication_token, content_encoding, lastnotification, iduser)'.
                ' VALUES(:endpoint, :public_key, :authentication_token, :content_encoding, :lastnotification, :iduser)';
        } else {
            $sql = 'UPDATE subscriptions SET endpoint=:endpoint, public_key=:public_key, authentication_token=:authentication_token, content_encoding=:content_encoding,'.
                ' lastnotification=:lastnotification, iduser=:iduser WHERE id = :id';
            $params[':id'] = $this->id;
        }

        $query = $DB->prepare($sql);
        if ($query->execute($params) === false) {
            // Enregistre le message d'erreur.
            $LOGGER->error(implode(', ', $query->errorInfo()));

            throw new \Exception('Une erreur est survenue lors de l\'enregistrement de la souscription.');
        }

        if (empty($this->id) === true) {
            $this->id = $DB->lastInsertId();
        }
    }
}
