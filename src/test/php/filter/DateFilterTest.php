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
use stubbles\input\filter\range\DateRange;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\DateFilter.
 *
 * @group  filter
 */
class DateFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  DateFilter
     */
    private $dateFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dateFilter = DateFilter::instance();
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
        assertNull($this->dateFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        $date = $this->dateFilter->apply($this->createParam('2008-09-27'));
        assertInstanceOf(Date::class, $date);
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
    public function applyReturnsNullForInvalidDate()
    {

        assertNull($this->dateFilter->apply($this->createParam('invalid date')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDate()
    {
        $param = $this->createParam('invalid date');
        $this->dateFilter->apply($param);
        assertTrue($param->hasError('DATE_INVALID'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = Date::now();
        assertEquals(
                $default,
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDate()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asDate();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asDate();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDateReturnsValidValue()
    {
        assertEquals(
                '2012-03-11',
                $this->readParam('2012-03-11')
                        ->asDate()
                        ->format('Y-m-d')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam(new Date('yesterday'))
                        ->asDate(new DateRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam(new Date('yesterday'))
             ->asDate(new DateRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
