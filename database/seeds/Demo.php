<?php
/**
 * Initialise un jeu de données de démonstration.
 *   Contexte :
 *     - Outils collaboratifs
 *       - Agenda
 *       - Messagerie
 *       - Portail d'information
 *       - Visio
 *       - Wiki
 *     - Applications formation/recherche
 *       - Emplois du temps
 *       - Plateforme de cours en ligne
 *     - Applications métiers
 *       - Gestion des étudiants
 *       - Gestion financière
 *       - Gestion des personnels
 *     - Réseau
 *       - Internet
 *       - Téléphonie
 *       - Wifi
 */

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Classe de remplissage de données pour Phinx.
 */
class Demo extends AbstractSeed {
    /**
     * Exécute la mise en place du scénario.
     *
     * @return void
     */
    public function run(): void {
        // Active le service thruk.
        $connection = $this->getAdapter()->getConnection();
        $statement = $connection->prepare('UPDATE plugins SET active = 1');
        $statement->execute();

        // Création des catégories.
        $data = array();
        $data[] = array('id' => 1, 'name' => 'Outils collaboratifs', 'position' => 1);
        $data[] = array('id' => 2, 'name' => 'Applications formation/recherche', 'position' => 2);
        $data[] = array('id' => 3, 'name' => 'Applications métiers', 'position' => 3);
        $data[] = array('id' => 4, 'name' => 'Réseau', 'position' => 4);

        $table = $this->table('categories');
        $table->insert($data)->save();

        // Création des services.
        $service = array('comment' => '', 'state' => '0', 'enable' => 1, 'visible' => 1, 'locked' => 0, 'timemodified' => date('Y-m-d\TH:i:s'), 'idplugin' => 1);

        $data = array();
        $data[] = array_merge(array('id' => 1, 'name' => 'Agenda', 'idcategory' => 1), $service);
        $data[] = array_merge(array('id' => 2, 'name' => 'Messagerie', 'idcategory' => 1), $service);
        $data[] = array_merge(array('id' => 3, 'name' => 'Portail d’information', 'idcategory' => 1), $service);
        $data[] = array_merge(array('id' => 4, 'name' => 'Visio', 'idcategory' => 1), $service);
        $data[] = array_merge(array('id' => 5, 'name' => 'Wiki', 'idcategory' => 1), $service);
        $data[] = array_merge(array('id' => 6, 'name' => 'Emplois du temps', 'idcategory' => 2), $service);
        $data[] = array_merge(array('id' => 7, 'name' => 'Plateforme de cours en ligne', 'idcategory' => 2), $service);
        $data[] = array_merge(array('id' => 8, 'name' => 'Gestion des étudiants', 'idcategory' => 3), $service);
        $data[] = array_merge(array('id' => 9, 'name' => 'Gestion financière', 'idcategory' => 3), $service);
        $data[] = array_merge(array('id' => 10, 'name' => 'Gestion des personnels', 'idcategory' => 3), $service);
        $data[] = array_merge(array('id' => 11, 'name' => 'Internet', 'idcategory' => 4), $service);
        $data[] = array_merge(array('id' => 12, 'name' => 'Téléphonie', 'idcategory' => 4), $service);
        $data[] = array_merge(array('id' => 13, 'name' => 'Wifi', 'idcategory' => 4), $service);

        $table = $this->table('services');
        $table->insert($data)->save();
    }
}
