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
class FixPluginsSettingsType extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Corrige le type du paramètre "tolerance".'.PHP_EOL;

        $sql = "UPDATE plugins_settings
                   SET type = 'integer'
                 WHERE key = 'tolerance'
                   AND idplugin IN (SELECT id FROM plugins WHERE codename = 'isou')";
        $this->execute($sql);
    }
}
