<?php
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

    /**
     * @return  scalar
     */
    public function getEmptyValues()
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
        assertNull($this->dayFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        $day = $this->dayFilter->apply($this->createParam('2008-09-27'));
        assertInstanceOf('stubbles\date\span\Day', $day);
        $date = $day->start();
        assertEquals(2008, $date->year());
        assertEquals(9, $date->month());
        assertEquals(27, $date->day());
        assertEquals(0, $date->hours());
        assertEquals(0, $date->minutes());
        assertEquals(0, $date->seconds());
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDay()
    {

        assertNull($this->dayFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        $this->dayFilter->apply($param);
        assertTrue($param->hasError('DATE_INVALID'));
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
        assertEquals(
                $default,
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDay()
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
        assertEquals(
                '2012-03-11',
                $this->readParam('2012-03-11')
                        ->asDay()
                        ->format('Y-m-d')
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
