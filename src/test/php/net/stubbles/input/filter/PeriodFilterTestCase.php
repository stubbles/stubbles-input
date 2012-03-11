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
use net\stubbles\lang\types\Date;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\PeriodFilter.
 *
 * @group  filter
 */
class PeriodFilterTestCase extends FilterTestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDateFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockDateFilter = $this->getMock('net\\stubbles\\input\\filter\\DateFilter');
    }

    /**
     * creates instance to test
     *
     * @param   Date  $minDate
     * @param   Date  $maxDate
     * @return  PeriodFilter
     */
    private function createPeriodFilter(Date $minDate = null, Date $maxDate = null)
    {
        return new PeriodFilter($this->mockDateFilter, $minDate, $maxDate);
    }

    /**
     * creates param
     *
     * @param   mixed $value
     * @param   Date  $date
     * @return  Param
     */
    protected function createDateParam($value, Date $date = null)
    {
        $param = parent::createParam($value);
        $this->mockDateFilter->expects($this->once())
                               ->method('apply')
                               ->with($this->equalTo($param))
                               ->will($this->returnValue($date));
        return $param;
    }

    /**
     * creates param
     *
     * @param   mixed $value
     * @param   Date  $date
     * @return  Param
     */
    protected function createParamWithoutMockPassing($value, Date $date)
    {
        $param = parent::createParam($value);
        $this->mockDateFilter->expects($this->once())
                               ->method('apply')
                               ->will($this->returnValue($date));
        return $param;
    }

    /**
     * @test
     */
    public function returnsNullIfDecoratedDateFilterReturnsNull()
    {
        $this->assertNull($this->createPeriodFilter()
                               ->apply($this->createDateParam(null, null))
        );
    }

    /**
     * @test
     */
    public function returnsDateIfNoPeriodRequirementsGiven()
    {
        $date = Date::now();
        $this->assertEquals($date,
                            $this->createPeriodFilter()
                                 ->apply($this->createDateParam('now', $date))
        );
    }

    /**
     * @test
     */
    public function returnsDateIfItDoesNotViolatePeriodRequirements()
    {
        $date = new Date('yesterday');
        $this->assertEquals($date,
                            $this->createPeriodFilter(new Date('2012-02-29'), new Date('tomorrow'))
                                 ->apply($this->createDateParam('yesterday', $date))
        );
    }

    /**
     * @test
     */
    public function returnsDateIfAfterMinDate()
    {
        $date = Date::now();
        $this->assertEquals($date,
                            $this->createPeriodFilter(new Date('2012-02-29'))
                                  ->apply($this->createDateParam('now', $date))
        );
    }

    /**
     * @test
     */
    public function returnsDateIfEqualToMinDate()
    {
        $date = Date::now();
        $this->assertEquals($date,
                            $this->createPeriodFilter($date)
                                 ->apply($this->createDateParam('now', $date))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfDateBeforeMinDate()
    {
        $this->assertNull($this->createPeriodFilter(Date::now())
                               ->apply($this->createParamWithoutMockPassing('yesterday', new Date('yesterday')))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenDateBeforeMinDate()
    {
        $param = $this->createParamWithoutMockPassing('yesterday', new Date('yesterday'));
        $this->createPeriodFilter(Date::now())->apply($param);
        $this->assertTrue($param->hasError('DATE_TOO_EARLY'));
    }

    /**
     * @test
     */
    public function returnsDateIfBeforeMaxDate()
    {
        $date = Date::now();
        $this->assertEquals($date,
                            $this->createPeriodFilter(null, new Date('tomorrow'))
                                 ->apply($this->createDateParam('now', $date))
        );
    }

    /**
     * @test
     */
    public function returnsDateIfEqualToMaxDate()
    {
        $date = Date::now();
        $this->assertEquals($date,
                            $this->createPeriodFilter(null, $date)
                                 ->apply($this->createDateParam('now', $date))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfDateAfterMaxDate()
    {
        $this->assertNull($this->createPeriodFilter(null, new Date('yesterday'))
                               ->apply($this->createParamWithoutMockPassing('now', Date::now()))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenDateAfterMaxDate()
    {
        $param = $this->createParamWithoutMockPassing('now', Date::now());
        $this->createPeriodFilter(null, new Date('yesterday'))->apply($param);
        $this->assertTrue($param->hasError('DATE_TOO_LATE'));
    }
}
?>