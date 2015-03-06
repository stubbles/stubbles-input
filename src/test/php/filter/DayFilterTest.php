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
        return [[''],
                [null]
        ];
    }

    /**
     * @param  scalar  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value)
    {
        $this->assertNull($this->dayFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        $day = $this->dayFilter->apply($this->createParam('2008-09-27'));
        $this->assertInstanceOf('stubbles\date\span\Day', $day);
        $date = $day->start();
        $this->assertEquals(2008, $date->year());
        $this->assertEquals(9, $date->month());
        $this->assertEquals(27, $date->day());
        $this->assertEquals(0, $date->hours());
        $this->assertEquals(0, $date->minutes());
        $this->assertEquals(0, $date->seconds());
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDay()
    {

        $this->assertNull($this->dayFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        $this->dayFilter->apply($param);
        $this->assertTrue($param->hasError('DATE_INVALID'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        $this->assertEquals(
                $default,
                $this->createValueReader(null)
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
        $this->assertNull($this->createValueReader(null)->required()->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asDay();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asDay();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDayReturnsValidValue()
    {
        $this->assertEquals(
                '2012-03-11',
                $this->createValueReader('2012-03-11')
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
        $this->assertNull(
                $this->createValueReader(new Day('yesterday'))
                        ->asDay(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueReader(new Day('yesterday'))
             ->asDay(new DatespanRange(Date::now(), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }
}
