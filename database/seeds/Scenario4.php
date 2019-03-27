<?php

/**
 * Initialise le scénario 4.
 *      Contexte du service isou :
 *          - 1 service Isou
 *          - 1 service Thruk
 */

use Phinx\Seed\AbstractSeed;

/**
 * Classe de remplissage de données pour Phinx.
 */
class Scenario4 extends AbstractSeed {
    const ISOU_SEED_PREFIX_ID = '4';
    const ISOU_SEED_NAME = 'Scenario '.self::ISOU_SEED_PREFIX_ID;

    public function run() {
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
                'name' => 'service "'.self::ISOU_SEED_NAME."'",
                'comment' => 'service dépendant d\'un service Thruk et d\'un service Isou',
                'state' => '0',
                'enable' => 1,
                'visible' => 1,
                'locked' => 0,
                'rsskey' => self::ISOU_SEED_PREFIX_ID.'1',
                'idplugin' => 1, // Type isou.
                'idcategory' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'name' => 'service isou ('.self::ISOU_SEED_NAME.')',
                'state' => '0',
                'enable' => 1,
                'visible' => 0, // Masqué.
                'locked' => 0,
                'rsskey' => self::ISOU_SEED_PREFIX_ID.'2',
                'idplugin' => 1, // Type isou.
                'idcategory' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'3',
                'name' => 'service thruk ('.self::ISOU_SEED_NAME.')',
                'state' => '0',
                'enable' => 1,
                'visible' => 0,
                'locked' => 0,
                'rsskey' => null,
                'idplugin' => 3, // Type thruk.
                'idcategory' => null,
            ),
        );
        $table = $this->table('services');
        $table->insert($data)->save();

        // Création des groupes de dépendance.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'name' => 'serveur isou - warning',
                'redundant' => '1',
                'groupstate' => '1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'name' => 'serveur isou - critical',
                'redundant' => '1',
                'groupstate' => '2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'3',
                'name' => 'serveur thruk - warning',
                'redundant' => '0',
                'groupstate' => '1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'2',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'4',
                'name' => 'serveur thruk - critical',
                'redundant' => '0',
                'groupstate' => '2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'2',
            ),
        );
        $table = $this->table('dependencies_groups');
        $table->insert($data)->save();

        // Création du contenu des groupes de dépendances.
        $data = array(
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Service isou.
                'servicestate' => 1,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Service isou.
                'servicestate' => 2,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'3',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'3', // Service thruk.
                'servicestate' => 1,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'4',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'3', // Service thruk.
                'servicestate' => 2,
            ),
        );
        $table = $this->table('dependencies_groups_content');
        $table->insert($data)->save();

        // Création des messages de dépendances.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'message' => 'service isou inaccessible',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'message' => 'service thruk inaccessible',
            ),
        );
        $table = $this->table('dependencies_messages');
        $table->insert($data)->save();
    }
}
