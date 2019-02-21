<?php

/**
  * Initialise le scénario 2.
  *      Contexte du service :
  *          - 1 serveur de base de données
  *          - 1 serveur web
  */

use Phinx\Seed\AbstractSeed;

/**
  * Classe de remplissage de données pour Phinx.
  */
class Scenario2 extends AbstractSeed {
    const ISOU_SEED_PREFIX_ID = '2';
    const ISOU_SEED_NAME = 'Scenario 2';

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
                'name' => 'service "'.self::ISOU_SEED_NAME.'"',
                'comment' => 'service dépendant d\'un serveur web et d\'une base de données',
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
                'name' => 'serveur db ('.self::ISOU_SEED_NAME.')',
                'state' => '0',
                'enable' => 1,
                'visible' => 0,
                'locked' => 0,
                'rsskey' => null,
                'idplugin' => 3, // Type thruk.
                'idcategory' => null,
                ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'3',
                'name' => 'serveur web ('.self::ISOU_SEED_NAME.')',
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
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'2',
                ),
            );
        $table = $this->table('dependencies_groups');
        $table->insert($data)->save();

        // Création du contenu des groupes de dépendances.
        $data = array(
            // Serveurs web.
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'3', // Serveur web 1.
                'servicestate' => 1,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'3', // Serveur web 1.
                'servicestate' => 2,
            ),
            // Serveur db.
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Serveur db.
                'servicestate' => 1,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Serveur db.
                'servicestate' => 2,
            ),
        );
        $table = $this->table('dependencies_groups_content');
        $table->insert($data)->save();

        // Création des messages de dépendances.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'message' => 'service instable',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'message' => 'service indisponible',
            ),
        );
        $table = $this->table('dependencies_messages');
        $table->insert($data)->save();
    }
}
