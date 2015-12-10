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
 * Tests for stubbles\input\filter\DatespanFilter.
 *
 * @group  filter
 * @since  4.3.0
 */
class DatespanFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  \stubbles\date\span\DatespanFilter
     */
    private $datespanFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->datespanFilter = DatespanFilter::instance();
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
        assertNull($this->datespanFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDayInstance()
    {
        $day = $this->datespanFilter->apply($this->createParam('2008-09-27'));
        assertInstanceOf(Day::class, $day);
        $date = $day->getStart();
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

        assertNull($this->datespanFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        $this->datespanFilter->apply($param);
        assertTrue($param->hasError('DATESPAN_INVALID'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        assertEquals(
                $default,
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDatespan()
        );
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asDatespan();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asDatespan();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsValidValue()
    {
        assertEquals(
                '2012-03-11',
                $this->readParam('2012-03-11')
                        ->asDatespan()
                        ->format('Y-m-d')
        );

    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam('yesterday')
                        ->asDatespan(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam('yesterday')
             ->asDatespan(new DatespanRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
