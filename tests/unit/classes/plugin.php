<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

namespace UniversiteRennes2\Isou\tests\unit;

use atoum;
use UniversiteRennes2\Mock\Logger;
use UniversiteRennes2\Mock\PDO;

$DB = new PDO();
$LOGGER = new Logger();

/**
 * Teste la classe Plugin.
 */
class Plugin extends atoum {
    /**
     * Teste la méthode __construct.
     *
     * @return void
     */
    public function test_construct() {
        // Rien à tester...
    }

    /**
     * Teste la méthode get_record.
     *
     * @return void
     */
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

    /**
     * Teste la méthode get_records.
     *
     * @return void
     */
    public function test_get_records() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records());

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->get_records(array('fetch_one' => true, 'id' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('codename' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('active' => true)));

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('id' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('codename' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('active' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('fetch_one' => true, 'foo' => '1'));
                    }
                );

        // TODO: Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
    }

    /**
     * Teste la méthode set_settings.
     *
     * @return void
     */
    public function test_set_settings() {
        // Rien à tester...
    }

    /**
     * Teste la méthode install.
     *
     * @return void
     */
    public function test_install() {
        // Rien à tester...
    }

    /**
     * Teste la méthode install_settings.
     *
     * @return void
     */
    public function test_install_settings() {
        // Rien à tester...
    }

    /**
     * Teste la méthode save.
     *
     * @return void
     */
    public function test_save() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé et que l'id a été incrémenté.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = null)
            ->then
                ->variable($this->testedInstance->id)->isNull();

        // Teste si un tableau est bien renvoyé et que l'id a été incrémenté.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 42)
            ->then
                ->variable($this->testedInstance->id)->isEqualTo(42);
    }

    /**
     * Teste la méthode update.
     *
     * @return void
     */
    public function test_update() {
        // Rien à tester...
    }

    /**
     * Teste la méthode update_settings.
     *
     * @return void
     */
    public function test_update_settings() {
        // Rien à tester...
    }
}
