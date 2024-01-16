<?php
/**
 * Initialise le scénario 5.
 *      Contexte du service isou :
 *          - 1 serveur web thruk
 *
 * Note: le service isou est verrouillé. Son état ne doit jamais changer.
 */

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Classe de remplissage de données pour Phinx.
 */
class Scenario5 extends AbstractSeed {
    const ISOU_SEED_PREFIX_ID = '5';
    const ISOU_SEED_NAME = 'Scenario '.self::ISOU_SEED_PREFIX_ID;

    /**
     * Exécute la mise en place du scénario.
     *
     * @return void
     */
    public function run(): void {
        // Création des catégories.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'name' => self::ISOU_SEED_NAME,
                'position' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
        );
        $table = $this->table('categories');
        $table->insert($data)->save();

        // Création des services.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'name' => 'service "'.self::ISOU_SEED_NAME.'"',
                'comment' => 'service dépendant d\'un serveur web',
                'state' => '0',
                'enable' => 1,
                'visible' => 1,
                'locked' => 1, // Verrouillé !
                'timemodified' => date('Y-m-d\TH:i:s'),
                'idplugin' => 1, // Plugin isou.
                'idcategory' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'name' => 'serveur web ('.self::ISOU_SEED_NAME.')',
                'state' => '0',
                'enable' => 1,
                'visible' => 0,
                'locked' => 0,
                'timemodified' => date('Y-m-d\TH:i:s'),
                'idplugin' => 3, // Plugin thruk.
                'idcategory' => null,
            ),
        );
        $table = $this->table('services');
        $table->insert($data)->save();

        // Création des groupes de dépendance.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'name' => 'service - warning',
                'redundant' => '0',
                'groupstate' => '1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'name' => 'service - critical',
                'redundant' => '0',
                'groupstate' => '2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
        );
        $table = $this->table('dependencies_groups');
        $table->insert($data)->save();

        // Création du contenu des groupes de dépendances.
        $data = array(
            // Serveurs web.
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Serveur web 1.
                'servicestate' => 1,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Serveur web 1.
                'servicestate' => 2,
            ),
        );
        $table = $this->table('dependencies_groups_content');
        $table->insert($data)->save();

        // Création des messages de dépendances.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'message' => 'service indisponible',
            ),
        );
        $table = $this->table('dependencies_messages');
        $table->insert($data)->save();
    }
}
