<?php
/**
 * Initialise le scénario 7.
 *      Contexte du service isou :
 *          - 2 serveur web redondés (service thruk)
 */

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Classe de remplissage de données pour Phinx.
 */
class Scenario7 extends AbstractSeed {
    const ISOU_SEED_PREFIX_ID = '7';
    const ISOU_SEED_NAME = 'Scenario 7';

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
                'name' => 'service "'.self::ISOU_SEED_NAME."'",
                'comment' => 'service dépendant de deux serveurs web et d\'une base de données',
                'state' => '0',
                'enable' => 1,
                'visible' => 1,
                'locked' => 0,
                'rsskey' => self::ISOU_SEED_PREFIX_ID.'1',
                'timemodified' => date('Y-m-d\TH:i:s'),
                'idplugin' => 1, // Type isou.
                'idcategory' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'name' => 'serveur web 1 ('.self::ISOU_SEED_NAME.')',
                'state' => '0',
                'enable' => 1,
                'visible' => 0,
                'locked' => 0,
                'rsskey' => null,
                'timemodified' => date('Y-m-d\TH:i:s'),
                'idplugin' => 2, // Type nagios.
                'idcategory' => null,
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'3',
                'name' => 'serveur web 2 ('.self::ISOU_SEED_NAME.')',
                'state' => '0',
                'enable' => 1,
                'visible' => 0,
                'locked' => 0,
                'rsskey' => null,
                'timemodified' => date('Y-m-d\TH:i:s'),
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
                'name' => 'serveurs web - warning',
                'redundant' => '1',
                'groupstate' => '1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
                'idmessage' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'2',
                'name' => 'serveurs web - critical',
                'redundant' => '1',
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
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'3', // Serveur web 2.
                'servicestate' => 1,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'2', // Serveur web 1.
                'servicestate' => 2,
            ),
            array(
                'idgroup' => self::ISOU_SEED_PREFIX_ID.'2',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'3', // Serveur web 2.
                'servicestate' => 2,
            ),
        );
        $table = $this->table('dependencies_groups_content');
        $table->insert($data)->save();

        // Création des messages de dépendances.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'message' => 'serveur web inaccessible',
            ),
        );
        $table = $this->table('dependencies_messages');
        $table->insert($data)->save();
    }
}
