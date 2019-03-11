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
use UniversiteRennes2\Mock\PDO;

$DB = new PDO();

/**
 * Classe pour tester la classe UniversiteRennes2\Isou\State.
 */
class State extends atoum {
    public function test_construct() {
        $i = 1;

        // Instance manuelle.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->id)->isEqualTo(0)
                ->variable($this->testedInstance->name)->isEqualTo('')
                ->variable($this->testedInstance->title)->isEqualTo('')
                ->variable($this->testedInstance->alternate_text)->isEqualTo('')
                ->variable($this->testedInstance->image)->isEqualTo('');
    }

    public function test_tostring() {
        $i = 1;

        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->__tostring())
                ->isEqualTo($this->testedInstance->get_flag_html_renderer());
    }

    public function test_get_flag_html_renderer() {
        $i = 1;

        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->get_flag_html_renderer())
                ->isEqualTo('<img src="/themes//images/" alt="" width="16px" height="16px" />');
    }

    public function test_get_record() {
        $i = 1;

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->get_record(array('id' => '1')));

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_record();
                    }
                );
    }

    public function test_get_records() {
        $i = 1;

        // Teste si un array est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records());

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->get_records(array('fetch_one' => true, 'id' => '1')));

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('id' => 'a'));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('foo' => '1'));
                    }
                );
    }
}
