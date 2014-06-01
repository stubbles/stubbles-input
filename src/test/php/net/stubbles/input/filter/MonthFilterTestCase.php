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
use stubbles\date\Date;
use stubbles\date\span\Month;
use net\stubbles\input\filter\range\DatespanRange;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\MonthFilter.
 *
 * @group  filter
 * @since  2.5.1
 */
class MonthFilterTestCase extends FilterTestCase
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
        $this->monthFilter = new MonthFilter();
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
        $this->assertNull($this->monthFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        $month = $this->monthFilter->apply($this->createParam('2008-09-27'));
        $this->assertInstanceOf('stubbles\date\span\Month', $month);
        $this->assertEquals('2008-09', $month->asString());
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidMonth()
    {
        $this->assertNull($this->monthFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        $this->monthFilter->apply($param);
        $this->assertTrue($param->hasError('MONTH_INVALID'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asMonth());
    }

    /**
     * @test
     */
    public function asMonthReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Month();
        $this->assertEquals($default,
                            $this->createValueReader(null)
                                 ->asMonth($default)
        );
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asMonth());
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asMonth();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')->asMonth());
    }

    /**
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asMonth();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asMonthReturnsValidValue()
    {
        $this->assertEquals('2012-03',
                            $this->createValueReader('2012-03-11')
                                 ->asMonth()
                                 ->asString()
        );

    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsOutOfRange()
    {
        $this->assertNull($this->createValueReader(new Month())
                               ->asMonth(null, new DatespanRange(new Date('tomorrow'), null))
        );
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueReader(new Month())
             ->asMonth(null, new DatespanRange(new Date('tomorrow'), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }
}
