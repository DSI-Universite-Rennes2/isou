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
 * Classe pour tester la classe UniversiteRennes2\Isou\Dependency_Group_Content.
 */
class Dependency_Group_Content extends atoum {
    public function test_construct() {
        $i = 1;

        // Instance manuelle.
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->id)->isEqualTo(0);
    }

    public function test_check_data() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->idgroup = '1')
            ->and($this->testedInstance->idservice = '1')
            ->and($this->testedInstance->servicestate = '1')
            ->then
                ->array($this->testedInstance->check_data(array('1' => 'foo'), array('1' => 'foo'), array('1' => 'foo')));

        // Teste les erreurs.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->idgroup = '1')
            ->and($this->testedInstance->idservice = '1')
            ->and($this->testedInstance->servicestate = '1')
            ->then
                ->array($this->testedInstance->check_data(array(), array(), array()))
                ->contains('Le groupe choisi est invalide.')
                ->contains('Le service choisi est invalide.')
                ->contains('L\'état choisi est invalide.');
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
                ->array($this->testedInstance->get_records(array('group' => '1')));

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
                        $this->testedInstance->get_records(array('group' => 1));
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

    public function test_save() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé et que l'id a été incrémenté.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 0)
            ->then
                ->array($this->testedInstance->save())
                    ->child['successes'](function($child) {
                        $child->hasSize(1)
                            ->contains('Les données ont été correctement enregistrées.');
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(0);
                    })
                ->variable($this->testedInstance->id)->isNotEqualTo(0);

        // Teste si un tableau est bien renvoyé et que l'id a été incrémenté.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 42)
            ->then
                ->array($this->testedInstance->save())
                    ->child['successes'](function($child) {
                        $child->hasSize(1)
                            ->contains('Les données ont été correctement enregistrées.');
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(0);
                    })
                ->variable($this->testedInstance->id)->isEqualTo(42);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 0)
            ->then
                ->array($this->testedInstance->save())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('Une erreur est survenue lors de l\'enregistrement des données.');
                    })
                ->variable($this->testedInstance->id)->isEqualTo(0);
    }

    public function test_change_state() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé et que l'id a été incrémenté.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->change_state($state = 1))
                    ->child['successes'](function($child) {
                        $child->hasSize(1)
                            ->contains('Les données ont été correctement enregistrées.');
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(0);
                    });

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->change_state($state = 1))
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('Une erreur est survenue lors de l\'enregistrement des données.');
                    });
    }

    public function test_delete() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 0)
            ->then
                ->array($this->testedInstance->delete())
                    ->child['successes'](function($child) {
                        $child->hasSize(1)
                            ->contains('Les données ont été correctement supprimées.');
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(0);
                    });

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 0)
            ->then
                ->array($this->testedInstance->delete())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('Une erreur est survenue lors de la suppression des données.');
                    })
                ->variable($this->testedInstance->id)->isEqualTo(0);
    }
}
