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
class AddNewSettings extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Initialise les nouvelles variables de gestion des flux.'.PHP_EOL;

        $table = $this->table('configuration');

        $rows = array(
            array(
                'key' => 'rss_enabled',
                'value' => '0',
                'type' => 'string',
            ),
            array(
                'key' => 'ical_enabled',
                'value' => '0',
                'type' => 'string',
            ),
            array(
                'key' => 'json_enabled',
                'value' => '0',
                'type' => 'string',
            ),
        );

        $table->insert($rows);
        $table->saveData();
    }
}
