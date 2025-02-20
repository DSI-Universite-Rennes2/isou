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
class RenameAuthField extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Renomme le champ "authentification" en "authentication" dans les tables "users" et "subscriptions".'.PHP_EOL;

        $table = $this->table('users');
        $table->renameColumn('authentification', 'authentication')
              ->save();

        $table = $this->table('subscriptions');
        $table->renameColumn('authentification_token', 'authentication_token')
              ->save();

        $sql = "UPDATE plugins SET type = 'authentication' WHERE type = 'authentification'";
        $this->execute($sql);
    }
}
