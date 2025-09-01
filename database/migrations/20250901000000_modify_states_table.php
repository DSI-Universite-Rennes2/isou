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
class ModifyStatesTable extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Ajoute un champ "bootstrapicon" et "bootstrapcolor" dans la table "states".'.PHP_EOL;

        $table = $this->table('states');
        $table->addColumn('bootstrapicon', 'string')
              ->addColumn('bootstrapcolor', 'string')
              ->save();


        echo PHP_EOL.' ## Insère les nouvelles valeurs dans les champs "bootstrapicon" et "bootstrapcolor" de la table "states".'.PHP_EOL;

        $values = array();
        $values[] = array('id' => 0, 'icon' => 'check-circle', 'color' => 'text-success');
        $values[] = array('id' => 1, 'icon' => 'exclamation-circle', 'color' => 'text-warning');
        $values[] = array('id' => 2, 'icon' => 'x-circle', 'color' => 'text-danger');
        $values[] = array('id' => 3, 'icon' => 'question-circle', 'color' => 'text-info');
        $values[] = array('id' => 4, 'icon' => 'pause-circle', 'color' => 'text-info');

        $connection = $this->getAdapter()->getConnection();
        foreach ($values as $data) {
            $statement = $connection->prepare('UPDATE states SET bootstrapicon = :icon, bootstrapcolor = :color WHERE id = :id');
            $statement->execute($data);
        }

        echo PHP_EOL.' ## Renomme l’état "Service instable ou indisponible" en "Service instable".'.PHP_EOL;

        $data = array(':new' => 'Service instable', ':old' => 'Service instable ou indisponible');

        $statement = $connection->prepare('UPDATE states SET title = :new WHERE id = 1 AND title = :old');
        $statement->execute($data);

        $statement = $connection->prepare('UPDATE states SET alternate_text = :new WHERE id = 1 AND alternate_text = :old');
        $statement->execute($data);

        echo PHP_EOL.' ## Renomme l’état "Etat du service non connu" en "État du service indéterminé".'.PHP_EOL;

        $data = array(':new' => 'État du service indéterminé', ':old' => 'Etat du service non connu');

        $statement = $connection->prepare('UPDATE states SET title = :new WHERE id = 3 AND title = :old');
        $statement->execute($data);

        $statement = $connection->prepare('UPDATE states SET alternate_text = :new WHERE id = 3 AND alternate_text = :old');
        $statement->execute($data);
    }
}
