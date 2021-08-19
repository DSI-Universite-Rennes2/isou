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
class AddNullableColumn extends AbstractMigration {
    /**
     * Autorise la valeur "null" pour la colonne "rsskey" de la table "services".
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Autorise la valeur "null" pour la colonne "rsskey" de la table "services".'.PHP_EOL;

        $table = $this->table('services');
        $table->changeColumn('rsskey', 'integer', array('null' => true))
            ->save();
    }
}
