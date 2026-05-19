<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Classe de migration pour Phinx.
 */
class AnnouncementAddTitleField extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Ajoute un champ "title" sur la table "announcement".'.PHP_EOL;

        $table = $this->table('announcement');
        $table->addColumn('title', 'string')
            ->save();
    }
}
