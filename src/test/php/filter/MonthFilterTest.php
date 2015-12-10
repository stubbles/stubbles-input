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
use stubbles\date\span\Month;
use stubbles\input\filter\range\DatespanRange;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\MonthFilter.
 *
 * @group  filter
 * @since  2.5.1
 */
class MonthFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  MonthFilter
     */
    private $monthFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->monthFilter = MonthFilter::instance();
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
        assertNull($this->monthFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsMonthInstance()
    {
        $month = $this->monthFilter->apply($this->createParam('2008-09-27'));
        assertInstanceOf(Month::class, $month);
        assertEquals('2008-09', $month->asString());
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidMonth()
    {
        assertNull($this->monthFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        $this->monthFilter->apply($param);
        assertTrue($param->hasError('MONTH_INVALID'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asMonth());
    }

    /**
     * @test
     */
    public function asMonthReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Month();
        assertEquals(
                $default,
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asMonth()
        );
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asMonth());
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asMonth();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asMonth());
    }

    /**
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asMonth();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asMonthReturnsValidValue()
    {
        assertEquals(
                '2012-03',
                $this->readParam('2012-03-11')
                        ->asMonth()
                        ->asString()
        );

    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam(new Month())
                        ->asMonth(new DatespanRange(new Date('tomorrow'), null))
        );
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam(new Month())
                ->asMonth(new DatespanRange(new Date('tomorrow'), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
