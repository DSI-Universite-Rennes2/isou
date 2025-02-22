<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou;

use Minishlink\WebPush\WebPush;

/**
 * Classe gérant les notifications web.
 */
class Notification {
    /**
      * Définis le TTL des messages à 5 minutes.
      */
    const TTL = 5 * 60;

    /**
     * Adresse vers laquelle sera acheminé le message.
     *
     * @var boolean
     */
    public $flush;

    /**
     * Tableau d'options des notifications web.
     *
     * @var array
     */
    public $options;

    /**
     * Tableau d'informations encodé en JSON qui sera envoyé au client.
     * Le tableau contient notamment le titre et le contenu du message de la notification web.
     *
     * @var string
     */
    public $payload;

    /**
     * Clé publique VAPID.
     *
     * @var boolean
     */
    public $public_key;

    /**
     * Clé privée VAPID.
     *
     * @var string
     */
    public $private_key;

    /**
     * Adresse du site hébergeant l'application.
     *
     * @var string
     */
    public $website_address;

    /**
     * Constructeur.
     *
     * @param string $title Titre de la notification web.
     * @param string $message Contenu du message de la notification web.
     * @param string $url Url du site envoyant la notification web.
     * @param string $icon Icône du site envoyant la notification web.
     *
     * @throws \Exception Lève une exception si les clés de chiffrement ne sont pas lisisbles.
     *
     * @return void
     */
    public function __construct(string $title, string $message, string $url, string $icon) {
        $payload = array();
        $payload['url'] = $url;
        $payload['icon'] = $icon;
        $payload['title'] = $title;
        $payload['message'] = $message;

        $this->payload = json_encode($payload);

        $this->flush = true;
        $this->options = array('TTL' => self::TTL);

        if (is_readable(VAPID_PRIVATE_KEY) === false) {
            throw new \Exception('Le fichier '.VAPID_PRIVATE_KEY.' n\'est pas lisible.');
        }
        $this->private_key = file_get_contents(VAPID_PRIVATE_KEY);

        if (is_readable(VAPID_PUBLIC_KEY) === false) {
            throw new \Exception('Le fichier '.VAPID_PUBLIC_KEY.' n\'est pas lisible.');
        }
        $this->public_key = file_get_contents(VAPID_PUBLIC_KEY);

        $this->website_address = $url;
    }

    /**
     * Retourne un objet Webpush prêt à envoyer des notifications.
     *
     * @return WebPush
     */
    public function get_webpush() {
        global $CFG;

        // Paramètres d'authentification du message.
        $authentication = array(
            'VAPID' => array(
                'subject' => $this->website_address,
                'publicKey' => $this->public_key,
                'privateKey' => $this->private_key,
            ),
        );

        $default_options = array();
        $timeout = 30;
        $curl_options = array('proxy' => array());

        if (empty($CFG['http_proxy']) === false) {
            $curl_options['proxy']['http'] = $CFG['http_proxy'];
        }

        if (empty($CFG['https_proxy']) === false) {
            $curl_options['proxy']['https'] = $CFG['https_proxy'];
        }

        if (empty($CFG['no_proxy']) === false) {
            $curl_options['proxy']['no'] = $CFG['no_proxy'];
        }

        return new WebPush($authentication, $default_options, $timeout, $curl_options);
    }
}
