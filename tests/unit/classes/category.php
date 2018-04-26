<?php
/*
 * This file is part of BIER project.
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
 * Classe pour tester la classe UniversiteRennes2\Isou\Category.
 */
class Category extends atoum {
    public function test_construct() {
        $i = 1;

        // Instance manuelle.
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->id)->isEqualTo(0);
    }

    public function test_get_services() {
        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
            ->then
                ->array($this->testedInstance->get_services());
    }

    public function test_check_data() {
        $i = 1;

        // Cas où ça fonctionne, avec une position à null.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = null)
            ->then
                ->array($this->testedInstance->check_data())
                ->isEmpty();

        // Cas où ça fonctionne, avec une position à 1.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = '1')
            ->then
                ->array($this->testedInstance->check_data())
                ->isEmpty();

        // Cas où le nom de la catégorie est vide.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = '')
            ->then
                ->array($this->testedInstance->check_data())
                ->contains('Le nom de la catégorie ne peut pas être vide.');

        // Cas où la position n'est ni null, ni un chiffre.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->position = 'a')
            ->then
                ->array($this->testedInstance->check_data())
                ->contains('Le nom de la catégorie ne peut pas être vide.');
    }

    public function test_get_record() {
        $i = 1;

        // Teste si un object est bien renvoyé.
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
                    });
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
                ->array($this->testedInstance->get_records(array('non-empty' => true)));

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
                    });

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('non-empty' => 1));
                    });

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('fetch_one' => true, 'foo' => '1'));
                    });

        // TODO: Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
    }

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

    public function test_delete() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = '1')
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
            ->and($this->testedInstance->id = '1')
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

    public function test_up() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = 2)
            ->then
                ->array($this->testedInstance->up())
                    ->child['successes'](function($child) {
                        $child->hasSize(1)
                            ->contains('Les données ont été correctement enregistrées.');
                        })
                    ->child['errors'](function($child) {
                        $child->hasSize(0);
                        });

        // Teste lorsque la catégorie est déjà au plus haut.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = 1)
            ->then
                ->array($this->testedInstance->up())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                        })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('La catégorie "'.$this->testedInstance->name.'" ne peut pas être montée davantage.');
                        });

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = 2)
            ->then
                ->array($this->testedInstance->up())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                        })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('Une erreur est survenue lors de l\'enregistrement des données.');
                        });
    }

    public function test_down() {
        global $DB;

        $i = 1;

        // Teste si un tableau est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = -1)
            ->then
                ->array($this->testedInstance->down())
                    ->child['successes'](function($child) {
                        $child->hasSize(1)
                            ->contains('Les données ont été correctement enregistrées.');
                        })
                    ->child['errors'](function($child) {
                        $child->hasSize(0);
                        });

        // Teste lorsque la catégorie est déjà au plus bas.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = 0)
            ->then
                ->array($this->testedInstance->down())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                        })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('La catégorie "'.$this->testedInstance->name.'" ne peut pas être descendue davantage.');
                        });

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->name = 'foo')
            ->and($this->testedInstance->position = -1)
            ->then
                ->array($this->testedInstance->down())
                    ->child['successes'](function($child) {
                        $child->hasSize(0);
                        })
                    ->child['errors'](function($child) {
                        $child->hasSize(1)
                            ->contains('Une erreur est survenue lors de l\'enregistrement des données.');
                        });
    }
}
