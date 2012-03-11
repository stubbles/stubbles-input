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
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\RangeFilter.
 *
 * @group  filter
 */
class RangeFilterTestCase extends FilterTestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockNumberFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockNumberFilter = $this->getMock('net\\stubbles\\input\\filter\\NumberFilter');
    }

    /**
     * creates instance to test
     *
     * @param   number  $minValue
     * @param   number  $maxValue
     * @return  RangeFilter
     */
    private function createRangeFilter($minValue = null, $maxValue = null)
    {
        return new RangeFilter($this->mockNumberFilter, $minValue, $maxValue);
    }

    /**
     * creates param
     *
     * @param   mixed $value
     * @return  Param
     */
    protected function createParam($value)
    {
        $param = parent::createParam($value);
        $this->mockNumberFilter->expects($this->once())
                               ->method('apply')
                               ->with($this->equalTo($param))
                               ->will($this->returnValue($value));
        return $param;
    }

    /**
     * creates param
     *
     * @param   mixed $value
     * @return  Param
     */
    protected function createParamWithoutMockPassing($value)
    {
        $param = parent::createParam($value);
        $this->mockNumberFilter->expects($this->once())
                               ->method('apply')
                               ->will($this->returnValue($value));
        return $param;
    }

    /**
     * @test
     */
    public function returnsNullIfDecoratedNumberFilterReturnsNull()
    {
        $this->assertNull($this->createRangeFilter()
                               ->apply($this->createParam(null))
        );
    }

    /**
     * @test
     */
    public function returnsNumberIfNoRangeRequirementsGiven()
    {
        $this->assertEquals(303,
                            $this->createRangeFilter()
                                 ->apply($this->createParam(303))
        );
    }

    /**
     * @test
     */
    public function returnsNumberIfItDoesNotViolateRangeRequirements()
    {
        $this->assertEquals(4,
                            $this->createRangeFilter(2, 5)
                                 ->apply($this->createParam(4))
        );
    }

    /**
     * @test
     */
    public function returnsNumberIfGreaterThanMinValue()
    {
        $this->assertEquals(303,
                            $this->createRangeFilter(10)
                                  ->apply($this->createParam(303))
        );
    }

    /**
     * @test
     */
    public function returnsNumberIfEqualToMinValue()
    {
        $this->assertEquals(303,
                            $this->createRangeFilter(303)
                                 ->apply($this->createParam(303))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfNumberSmallerThanMinValue()
    {
        $this->assertNull($this->createRangeFilter(4)
                               ->apply($this->createParamWithoutMockPassing(3))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenStringShorterThanMinLength()
    {
        $param = $this->createParamWithoutMockPassing(3);
        $this->createRangeFilter(4)->apply($param);
        $this->assertTrue($param->hasError('VALUE_TOO_SMALL'));
    }

    /**
     * @test
     */
    public function returnsNumberIfSmallerThanMaxValue()
    {
        $this->assertEquals(99,
                            $this->createRangeFilter(null, 100)
                                 ->apply($this->createParam(99))
        );
    }

    /**
     * @test
     */
    public function returnsNumberIfEqualToMaxValue()
    {
        $this->assertEquals(3,
                            $this->createRangeFilter(null, 3)
                                 ->apply($this->createParam(3))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfNumberGreaterThanMaxValue()
    {
        $this->assertNull($this->createRangeFilter(null, 2)
                               ->apply($this->createParamWithoutMockPassing(3))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenNumberGreaterThanMaxValue()
    {
        $param = $this->createParamWithoutMockPassing(3);
        $this->createRangeFilter(null, 2)->apply($param);
        $this->assertTrue($param->hasError('VALUE_TOO_GREAT'));
    }
}
?>