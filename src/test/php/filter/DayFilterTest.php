<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\date\Date;
use stubbles\date\span\Day;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\DayFilter.
 *
 * @group  filter
 */
class DayFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  DayFilter
     */
    private $dayFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dayFilter = DayFilter::instance();
        parent::setUp();
    }

    public function getEmptyValues(): array
    {
        return [[''], [null]];
    }

    /**
     * @param  scalar  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value)
    {
        assertNull($this->dayFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        assert(
                $this->dayFilter->apply($this->createParam('2008-09-27'))[0],
                equals(new Day('2008-09-27'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDay()
    {

        assertNull($this->dayFilter->apply($this->createParam('invalid day'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->dayFilter->apply($param);
        assertTrue(isset($errors['DATE_INVALID']));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        assert(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDay(),
                equals($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asDay();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asDay();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDayReturnsValidValue()
    {
        assert(
                $this->readParam('2012-03-11')
                        ->asDay()
                        ->format('Y-m-d'),
                equals('2012-03-11')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam(new Day('yesterday'))
                        ->asDay(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam(new Day('yesterday'))
             ->asDay(new DatespanRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
