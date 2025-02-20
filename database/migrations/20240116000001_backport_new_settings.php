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
class BackportNewSettings extends AbstractMigration {
    /**
     * Méthode effectuant des modifications dans la base de données.
     *
     * @throws Exception Lève une exception en cas d'erreur.
     *
     * @return void
     */
    public function change() {
        // Note : ces paramètres ont déjà été introduits lors du processus de mise à jour en version 3.3.0. Par contre, ils n'étaient pas créés pour les nouvelles installations.
        echo PHP_EOL.' #';
        echo PHP_EOL.' ## Initialise de nouveaux paramètres.'.PHP_EOL;

        $now = date('Y-m-d\TH:i:s');

        $table = $this->table('configuration');

        $rows = array(
            array(
                'key' => 'check_updates_enabled',
                'value' => '0',
                'type' => 'boolean',
            ),
            array(
                'key' => 'last_update_check',
                'value' => $now,
                'type' => 'datetime',
            ),
            array(
                'key' => 'available_update',
                'value' => '0',
                'type' => 'string',
            ),
            array(
                'key' => 'gather_statistics_enabled',
                'value' => '0',
                'type' => 'boolean',
            ),
            array(
                'key' => 'last_statistics_gathering',
                'value' => $now,
                'type' => 'datetime',
            ),
        );

        foreach ($rows as $row) {
            $sql = sprintf('SELECT * FROM configuration WHERE key = "%s"', $row['key']);
            if ($this->fetchRow($sql) !== false) {
                continue;
            }

            $table->insert(array($row));
        }
        $table->saveData();

        // Supprime un paramètre obsolète.
        $sql = "DELETE FROM configuration WHERE key = 'last_check_update'";
        $this->execute($sql);
    }
}
