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
class AnnouncementDropVisibleField extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Supprime le champ "visible" de la table "announcement".'.PHP_EOL;

        $rows = $this->query('SELECT * FROM announcement');
        foreach ($rows as $row) {
            $visible = $row['visible'];
        }

        $table = $this->table('announcement');
        $table->removeColumn('visible')
            ->save();

        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Ajoute un champ "startdate" et "enddate" à la table "announcement".'.PHP_EOL;
        $table->addColumn('startdate', 'string')
            ->addColumn('enddate', 'string')
            ->save();

        $startdate = null;
        $enddate = null;
        if (empty($visible) === false) {
            // L'annonce doit être affichée.
            $startdate = date('Y-m-d\TH:i:00');
        }

        $connection = $this->getAdapter()->getConnection();
        $statement = $connection->prepare('UPDATE announcement SET startdate=:startdate, enddate=:enddate');
        $statement->execute(array(':startdate' => $startdate, ':enddate' => $enddate));
    }
}
