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
use UniversiteRennes2\Isou\State;
use UniversiteRennes2\Mock\Logger;
use UniversiteRennes2\Mock\PDO;

$DB = new PDO();
$LOGGER = new Logger();

/**
 * Teste la classe Dependency_Group.
 */
class Dependency_Group extends atoum {
    /**
     * Teste la méthode __construct.
     *
     * @return void
     */
    public function test_construct() {
        $i = 1;

        // Instance manuelle.
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->id)->isEqualTo(0);
    }

    /**
     * Teste la méthode check_data.
     *
     * @return void
     */
    public function test_check_data() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->redundant = '1')
            ->and($this->testedInstance->groupstate = '1')
            ->and($this->testedInstance->idservice = '1')
            ->then
                ->array($this->testedInstance->check_data(array('1' => 'foo'), array('1' => 'foo'), array('1' => 'foo')));

        // Teste les erreurs.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = '')
            ->and($this->testedInstance->redundant = '1')
            ->and($this->testedInstance->groupstate = '1')
            ->and($this->testedInstance->idservice = '1')
            ->then
                ->array($this->testedInstance->check_data(array(), array(), array()))
                ->contains('Le nom du groupe ne peut pas être vide.')
                ->contains('La valeur "redondée" choisie est invalide.')
                ->contains('L\'état choisi est invalide.')
                ->contains('Le service choisi est invalide.');
    }

    /**
     * Teste la méthode get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags.
     *
     * @return void
     */
    public function test_get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags() {
        // Ce test génère une boucle infinie à cause du `while ($group = $query->fetch())`.
        return;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_dependencies_groups_and_groups_contents_by_service_sorted_by_flags($idservice = 1));
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
                ->array($this->testedInstance->get_records(array('service' => '1')));

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
                        $this->testedInstance->get_records(array('service' => 1));
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
     * Teste la méthode get_service_reverse_dependency_groups.
     *
     * @return void
     */
    public function test_get_service_reverse_dependency_groups() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_service_reverse_dependency_groups($idservice = '1', $state = State::WARNING));
    }

    /**
     * Teste la méthode set_message.
     *
     * @return void
     */
    public function test_set_message() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->integer($this->testedInstance->set_message());

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->set_message())->isEqualTo(false);
    }

    /**
     * Teste la méthode save.
     *
     * @return void
     */
    public function test_save() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé et que l'id inexistant a été incrémenté.
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

        // Teste si un tableau est bien renvoyé et que l'id existant n'a pas été incrémenté.
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

    /**
     * Teste la méthode duplicate.
     *
     * @return void
     */
    public function test_duplicate() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->and($this->testedInstance->groupstate = State::WARNING)
            ->then
                ->array($this->testedInstance->duplicate());

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->and($this->testedInstance->groupstate = State::CRITICAL)
            ->then
                ->array($this->testedInstance->duplicate());
    }

    /**
     * Teste la méthode delete.
     *
     * @return void
     */
    public function test_delete() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
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
            ->then
                ->array($this->testedInstance->delete())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                    })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('Une erreur est survenue lors de la suppression des données.');
                    });
    }

    /**
     * Teste la méthode is_up.
     *
     * @return void
     */
    public function test_is_up() {
        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->and($this->testedInstance->redundant = '0')
            ->then
                ->boolean($this->testedInstance->is_up())->isEqualTo(true);

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->and($this->testedInstance->redundant = '1')
            ->then
                ->boolean($this->testedInstance->is_up())->isEqualTo(false);
    }
}
