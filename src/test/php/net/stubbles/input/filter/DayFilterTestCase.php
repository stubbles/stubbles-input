<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\filter\range\DatespanRange;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Day;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\DayFilter.
 *
 * @group  filter
 */
class DayFilterTestCase extends FilterTestCase
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
        $this->dayFilter = new DayFilter();
        parent::setUp();
    }

    /**
     * @return  scalar
     */
    public function getEmptyValues()
    {
        return array(array(''),
                     array(null)
        );
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
        $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Day', $day);
        $date = $day->getStart();
        $this->assertEquals(2008, $date->getYear());
        $this->assertEquals(9, $date->getMonth());
        $this->assertEquals(27, $date->getDay());
        $this->assertEquals(0, $date->getHours());
        $this->assertEquals(0, $date->getMinutes());
        $this->assertEquals(0, $date->getSeconds());
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
        $this->assertNull($this->createValueFilter(null)->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        $this->assertEquals($default,
                            $this->createValueFilter(null)
                                 ->asDay($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asDay();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asDay();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDayReturnsValidValue()
    {
        $this->assertEquals('2012-03-11',
                            $this->createValueFilter('2012-03-11')
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
        $this->assertNull($this->createValueFilter(new Day('yesterday'))
                               ->asDay(null, new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueFilter(new Day('yesterday'))
             ->asDay(null, new DatespanRange(Date::now(), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }
}
?>