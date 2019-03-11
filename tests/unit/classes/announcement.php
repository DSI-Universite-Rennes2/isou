<?php
/*
 * This file is part of Isou project.
 *
 * (c) Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UniversiteRennes2\Isou\tests\unit;

use atoum;
use UniversiteRennes2\Mock\Logger;
use UniversiteRennes2\Mock\PDO;

$DB = new PDO();
$LOGGER = new Logger();

/**
 * Classe pour tester la classe UniversiteRennes2\Isou\Announcement.
 */
class Announcement extends atoum {
    public function test_construct() {
        $i = 1;

        // Instance manuelle.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->last_modification = '01/01/1970')
            ->then
                ->if($this->testedInstance->__construct())
                    ->dateTime($this->testedInstance->last_modification);
    }

    public function test_check_data() {
        $i = 1;

        // Vérifie que la visibilité est bien valide.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->visible = '2')
            ->then
                ->array($this->testedInstance->check_data(array('1' => 'visible')))
                ->isNotEmpty();

        // Vérifie que la visibilité est bien sur 0, lorsque le message est vide.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->message = '')
            ->and($this->testedInstance->visible = '1')
            ->then
                ->array($this->testedInstance->check_data(array('1' => 'visible')))
                ->isEmpty()
                ->variable($this->testedInstance->visible)->isEqualTo('0');
    }

    public function test_get_record() {
        $i = 1;

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->get_record());

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->get_record(array('empty' => true, 'visible' => false)));

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->get_record(array('empty' => false, 'visible' => true)));

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_record(array('empty' => '1'));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_record(array('visible' => '1'));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_record(array('fetch_one' => true, 'foo' => '1'));
                    }
                );
    }

    public function test_save() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->visible = '1')
            ->then
                ->array($this->testedInstance->save())
                    ->array['successes'];
                    // ->contains('L\'annonce a bien été enregistrée.');

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->visible = '0')
            ->then
                ->array($this->testedInstance->save())
                    ->array['successes'];
                    // ->contains('L\'annonce a bien été retirée.');

        // Teste si un tableau est bien renvoyé.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->save())
                    ->array['errors'];
    }
}
