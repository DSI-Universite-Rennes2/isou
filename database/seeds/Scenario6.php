<?php

/**
 * Initialise le scénario 6.
 *
 * Description:
 *    On a un service Isou qui fonctionne.
 *    Un évènement prévu est en cours.
 *    Une fois le cron lancé, le service isou doit passer en rouge.
 */

use Phinx\Seed\AbstractSeed;

/**
 * Classe de remplissage de données pour Phinx.
 */
class Scenario6 extends AbstractSeed {
    const ISOU_SEED_PREFIX_ID = '6';
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
                'name' => 'service "'.self::ISOU_SEED_NAME.'"',
                'comment' => 'service ayant un évènement en cours',
                'state' => '0',
                'enable' => 1,
                'visible' => 1,
                'locked' => 0,
                'rsskey' => self::ISOU_SEED_PREFIX_ID.'1',
                'idplugin' => 1, // Plugin isou.
                'idcategory' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
        );
        $table = $this->table('services');
        $table->insert($data)->save();

        // Création des groupes de dépendance.
        $data = array(
            array(
                'id' => self::ISOU_SEED_PREFIX_ID.'1',
                'startdate' => strftime('%FT%T', time() - 2 * 60),
                'enddate' => strftime('%FT%T', time() + 2 * 60),
                'state' => '2',
                'type' => '1', // TYPE_SCHEDULED.
                'period' => '0', // PERIOD_NONE.
                'ideventdescription' => '1',
                'idservice' => self::ISOU_SEED_PREFIX_ID.'1',
            ),
        );
        $table = $this->table('events');
        $table->insert($data)->save();
    }
}
