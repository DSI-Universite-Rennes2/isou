<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Classe de migration pour Phinx.
 */
class ProxySettings extends AbstractMigration {
    /**
     * Initialise les variables de proxy.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Initialise les variables de proxy.'.PHP_EOL;

        $table = $this->table('configuration');

        $rows = array(
            array(
                'key' => 'http_proxy',
                'value' => '',
                'type' => 'string',
            ),
            array(
                'key' => 'https_proxy',
                'value' => '',
                'type' => 'string',
            ),
            array(
                'key' => 'no_proxy',
                'value' => '',
                'type' => 'array',
            ),
        );

        $table->insert($rows);
        $table->saveData();
    }
}
