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
use UniversiteRennes2\Isou\Event as IsouEvent;
use UniversiteRennes2\Isou\State;
use UniversiteRennes2\Mock\Logger;
use UniversiteRennes2\Mock\PDO;

$DB = new PDO();
$LOGGER = new Logger();

/**
 * Teste la classe Event.
 */
class Event extends atoum {
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

         // Instance PDO.
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 1)
            ->then
                ->variable($this->testedInstance->id)->isEqualTo(1);
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
        $datetime = new \DateTime();

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
                ->array($this->testedInstance->get_records(array('after' => $datetime)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('before' => $datetime)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('enddate_between' => array($datetime, $datetime))));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('idservice' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('regular' => true)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('regular' => false)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('plugin' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('notplugin' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('since' => $datetime)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('finished' => true)));


        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('state' => '1')));


        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('tolerance' => 1)));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('type' => '1')));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->get_records(array('sort' => array('id ASC'))));

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
                        $this->testedInstance->get_records(array('after' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('before' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('enddate_between' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $datetime = new \DateTime();
                        $this->testedInstance->get_records(array('enddate_between' => array()));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $datetime = new \DateTime();
                        $this->testedInstance->get_records(array('enddate_between' => array(1, $datetime)));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $datetime = new \DateTime();
                        $this->testedInstance->get_records(array('enddate_between' => array($datetime, 1)));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('idservice' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('regular' => 1));
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
                        $this->testedInstance->get_records(array('notplugin' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('since' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('finished' => 1));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('state' => 42));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('tolerance' => '1'));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('type' => 42));
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->get_records(array('sort' => 1));
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
     * Teste la méthode is_now.
     *
     * @return void
     */
    public function test_is_now() {
        $i = 1;

        $now = '2000-01-01T00:00:01';
        $datetime_before = new \DateTime('2000-01-01T00:00:00');
        $datetime_now = new \DateTime($now);
        $datetime_after = new \DateTime('2000-01-01T00:00:02');

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->is_now());

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->is_now($now));

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->is_now('foo'));

        // Teste si vrai est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = $datetime_before)
            ->then
                ->boolean($this->testedInstance->is_now($now))->isTrue();

        // Teste si vrai est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = $datetime_now)
            ->then
                ->boolean($this->testedInstance->is_now($now))->isTrue();

        // Teste si vrai est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = $datetime_after)
            ->and($this->testedInstance->enddatedate = $datetime_after)
            ->then
                ->boolean($this->testedInstance->is_now($now))->isFalse();

        // Teste si vrai est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = $datetime_after)
            ->and($this->testedInstance->enddatedate = null)
            ->then
                ->boolean($this->testedInstance->is_now($now))->isFalse();

        // Teste si faux est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = $datetime_after)
            ->then
                ->boolean($this->testedInstance->is_now($now))->isFalse();
    }

    /**
     * Teste la méthode set_service.
     *
     * @return void
     */
    public function test_set_service() {
        $i = 1;

        // Teste si la méthode s'exécute correctement.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->set_service('1', array('1' => 'foo')));

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_service('1');
                    }
                );
    }

    /**
     * Teste la méthode set_period.
     *
     * @return void
     */
    public function test_set_period() {
        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_SCHEDULED)
            ->then($this->testedInstance->set_period(''))
                ->variable($this->testedInstance->period)->isEqualTo(IsouEvent::PERIOD_NONE);

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_REGULAR)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_period('');
                    }
                );

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = new \DateTime('2000-01-01T00:00:00'))
            ->and($this->testedInstance->enddate = new \DateTime('2000-01-01T03:00:00'))
            ->and($this->testedInstance->type = IsouEvent::TYPE_REGULAR)
            ->then($this->testedInstance->set_period(IsouEvent::PERIOD_DAILY))
                ->variable($this->testedInstance->period)->isEqualTo(IsouEvent::PERIOD_DAILY);

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_period('42');
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_SCHEDULED)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_period(IsouEvent::PERIOD_DAILY);
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_REGULAR)
            ->and($this->testedInstance->enddate = null)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_period(IsouEvent::PERIOD_DAILY);
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_REGULAR)
            ->and($this->testedInstance->startdate = new \DateTime('2000-01-01T00:00:00'))
            ->and($this->testedInstance->enddate = new \DateTime('2000-03-01T03:00:00'))
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_period(IsouEvent::PERIOD_DAILY);
                    }
                );
    }

    /**
     * Teste la méthode set_type.
     *
     * @return void
     */
    public function test_set_type() {
        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->set_type(IsouEvent::TYPE_SCHEDULED))
                ->variable($this->testedInstance->type)->isEqualTo(IsouEvent::TYPE_SCHEDULED);

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_type(42);
                    }
                );
    }

    /**
     * Teste la méthode set_startdate.
     *
     * @return void
     */
    public function test_set_startdate() {
        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->set_startdate('2000-01-01', '00:00'))
                ->variable($this->testedInstance->startdate)->isEqualTo(new \DateTime('2000-01-01T00:00'));

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_startdate('foo', 'bar');
                    }
                );
    }

    /**
     * Teste la méthode set_enddate.
     *
     * @return void
     */
    public function test_set_enddate() {
        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = new \DateTime('2000-01-01T00:00:00'))
            ->then($this->testedInstance->set_enddate('2000-01-01', '01:00'))
                ->variable($this->testedInstance->enddate)->isEqualTo(new \DateTime('2000-01-01T01:00'));

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->set_enddate('', ''))
                ->variable($this->testedInstance->enddate)->isNull();

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_enddate('foo', 'bar');
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_REGULAR)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_enddate('', '');
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->startdate = new \DateTime('2000-01-01T10:00:00'))
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_enddate('2000-01-01', '00:00');
                    }
                );
    }

    /**
     * Teste la méthode set_state.
     *
     * @return void
     */
    public function test_set_state() {
        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->state = State::WARNING)
            ->then($this->testedInstance->set_state(State::OK))
                ->variable($this->testedInstance->state)->isEqualTo(State::OK);

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_state('foo');
                    }
                );

        // Teste lorsque les paramètres ne sont pas corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->type = IsouEvent::TYPE_CLOSED)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->set_state(State::OK);
                    }
                );
    }

    /**
     * Teste la méthode set_description.
     *
     * @return void
     */
    public function test_set_description() {
        global $DB;

        $i = 1;

        $DB->test_pdostatement->test_fetch = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->set_description('foo'))
                ->variable($this->testedInstance->description->description)->isEqualTo('foo');
    }

    /**
     * Teste la méthode save.
     *
     * @return void
     */
    public function test_save() {
        global $DB;

        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 0)
            ->and($this->testedInstance->type = IsouEvent::TYPE_UNSCHEDULED)
            ->and($this->testedInstance->enddate = new \DateTime('2000-01-01T00:00:00'))
            ->then($this->testedInstance->save())
                ->variable($this->testedInstance->id)->isNotEqualTo(0);

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->id = 42)
            ->and($this->testedInstance->type = IsouEvent::TYPE_UNSCHEDULED)
            ->then($this->testedInstance->save())
                ->variable($this->testedInstance->id)->isEqualTo(42);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->save();
                    }
                );
    }

    /**
     * Teste la méthode delete.
     *
     * @return void
     */
    public function test_delete() {
        global $DB;

        $i = 1;

        // Teste lorsque les paramètres sont corrects.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->delete());

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() {
                        $this->testedInstance->delete();
                    }
                );
    }

    /**
     * Teste la méthode close.
     *
     * @return void
     */
    public function test_close() {
        global $DB;

        $i = 1;

        // Teste si un booléen est bien renvoyé.
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->close())->isEqualTo(true);

        // Teste lorsque la requête SQL échoue.
        $DB->test_pdostatement->test_execute = false;
        $this->assert(__METHOD__.' : test #'.$i++)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->close())->isEqualTo(false);
    }
}
