<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
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
 * Teste la classe Service.
 */
class Service extends atoum {
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
     * Teste la méthode __tostring.
     *
     * @return void
     */
    public function test_tostring() {
        $i = 1;

        // Instance manuelle.
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->__tostring());
    }

    /**
     * Teste la méthode check_data.
     *
     * @return void
     */
    public function test_check_data() {
        global $DB;

        $i = 1;

        // Cas où ça fonctionne.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->idplugin = PLUGIN_ISOU)
            ->and($this->testedInstance->visible = '1')
            ->and($this->testedInstance->locked = '1')
            ->and($this->testedInstance->idcategory = '1')
            ->and($this->testedInstance->url = '')
            ->then
                ->array($this->testedInstance->check_data())
                ->isEmpty();

        // Cas où ça fonctionne.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->idplugin = '2')
            ->and($this->testedInstance->visible = '1')
            ->and($this->testedInstance->locked = '1')
            ->and($this->testedInstance->idcategory = '1')
            ->then
                ->array($this->testedInstance->check_data())
                ->isEmpty();

        // Teste lorsque les paramètres ne sont pas corrects.
        $DB->test_pdostatement->test_fetch = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->idplugin = PLUGIN_ISOU)
            ->and($this->testedInstance->visible = '1')
            ->and($this->testedInstance->locked = '1')
            ->and($this->testedInstance->idcategory = '1')
            ->then
                ->array($this->testedInstance->check_data())
                ->contains('Le type de service choisi est invalide.');

        // Teste lorsque les paramètres ne sont pas corrects.
        $DB->test_pdostatement->test_execute = true;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->idplugin = PLUGIN_ISOU)
            ->and($this->testedInstance->visible = 1)
            ->and($this->testedInstance->locked = '1')
            ->and($this->testedInstance->idcategory = '1')
            ->then
                ->array($this->testedInstance->check_data())
                ->contains('La valeur choisie pour la visibilité n\'est pas valide.');

        // Teste lorsque les paramètres ne sont pas corrects.
        $DB->test_pdostatement->test_execute = true;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->idplugin = PLUGIN_ISOU)
            ->and($this->testedInstance->visible = '1')
            ->and($this->testedInstance->locked = 1)
            ->and($this->testedInstance->idcategory = '1')
            ->then
                ->array($this->testedInstance->check_data())
                ->contains('La valeur choisie pour le verrouillage n\'est pas valide.');
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
                ->array($this->testedInstance->get_records(array('enable' => true)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('locked' => true)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('state' => State::OK)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('visible' => true)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('category' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('plugin' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('dependencies_group' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('fetch_column' => true)));

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
                        $this->testedInstance->get_records(array('id' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('enable' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('locked' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('state' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('visible' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('category' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('plugin' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('dependencies_group' => 1));
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
     * Teste la méthode change_state.
     *
     * @return void
     */
    public function test_change_state() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->boolean($this->testedInstance->change_state(State::OK));

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->change_state(State::OK);
                    }
                );
    }

    /**
     * Teste la méthode enable.
     *
     * @return void
     */
    public function test_enable() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->enable())->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->enable())->isEqualTo(false);
    }

    /**
     * Teste la méthode disable.
     *
     * @return void
     */
    public function test_disable() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->disable())->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->disable())->isEqualTo(false);
    }

    /**
     * Teste la méthode visible.
     *
     * @return void
     */
    public function test_visible() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->visible())->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->visible())->isEqualTo(false);
    }

    /**
     * Teste la méthode hide.
     *
     * @return void
     */
    public function test_hide() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->hide())->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->hide())->isEqualTo(false);
    }

    /**
     * Teste la méthode lock.
     *
     * @return void
     */
    public function test_lock() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->lock(State::OK))->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->lock(State::OK))->isEqualTo(false);
    }

    /**
     * Teste la méthode unlock.
     *
     * @return void
     */
    public function test_unlock() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->unlock())->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->unlock())->isEqualTo(false);
    }

    /**
     * Teste la méthode get_dependencies.
     *
     * @return void
     */
    public function test_get_dependencies() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->array($this->testedInstance->get_dependencies());
    }

    /**
     * Teste la méthode get_reverse_dependencies.
     *
     * @return void
     */
    public function test_get_reverse_dependencies() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_reverse_dependencies());
    }

    /**
     * Teste la méthode set_reverse_dependencies.
     *
     * @return void
     */
    public function test_set_reverse_dependencies() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->set_reverse_dependencies())
                ->variable($this->testedInstance->reverse_dependencies)->isEqualTo($this->testedInstance->get_reverse_dependencies());
    }

    /**
     * Teste la méthode get_all_events.
     *
     * @return void
     */
    public function test_get_all_events() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->array($this->testedInstance->get_all_events());
    }

    /**
     * Teste la méthode get_current_event.
     *
     * @return void
     */
    public function test_get_current_event() {
        $i = 1;

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->object($this->testedInstance->get_current_event());
    }

    /**
     * Teste la méthode get_closed_event.
     *
     * @return void
     */
    public function test_get_closed_event() {
        $i = 1;

        // Teste si un objet est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->object($this->testedInstance->get_closed_event());
    }

    /**
     * Teste la méthode get_regular_events.
     *
     * @return void
     */
    public function test_get_regular_events() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->array($this->testedInstance->get_regular_events());
    }
}
