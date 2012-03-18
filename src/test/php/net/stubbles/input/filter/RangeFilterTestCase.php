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
use net\stubbles\input\ParamError;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\RangeFilter.
 *
 * @group  filter
 */
class RangeFilterTestCase extends FilterTestCase
{
    /**
     * instance to test
     *
     * @type  RangeFilter
     */
    private $rangeFilter;
    /**
     * mocked decorated filter
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFilter;
    /**
     * mocked range definition
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRange;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockFilter  = $this->getMock('net\\stubbles\\input\\filter\\Filter');
        $this->mockRange   = $this->getMock('net\\stubbles\\input\\filter\\Range');
        $this->rangeFilter = new RangeFilter($this->mockFilter, $this->mockRange);
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
        $this->mockFilter->expects($this->once())
                               ->method('apply')
                               ->will($this->returnValue($value));
        return $param;
    }

    /**
     * @test
     */
    public function returnsNullIfDecoratedNumberFilterReturnsNull()
    {
        $this->mockRange->expects($this->never())
                        ->method('belowMinBorder');
        $this->mockRange->expects($this->never())
                        ->method('getMinParamError');
        $this->mockRange->expects($this->never())
                        ->method('aboveMaxBorder');
        $this->mockRange->expects($this->never())
                        ->method('getMaxParamError');
        $this->assertNull($this->rangeFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsValueIfItDoesNotViolateRangeRequirements()
    {
        $this->mockRange->expects($this->once())
                        ->method('belowMinBorder')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(false));
        $this->mockRange->expects($this->never())
                        ->method('getMinParamError');
        $this->mockRange->expects($this->once())
                        ->method('aboveMaxBorder')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(false));
        $this->mockRange->expects($this->never())
                        ->method('getMaxParamError');
        $this->assertEquals(303,
                            $this->rangeFilter->apply($this->createParam(303))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfNumberBelowMinBorder()
    {
        $param = $this->createParam(303);
        $this->mockRange->expects($this->once())
                        ->method('belowMinBorder')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(true));
        $this->mockRange->expects($this->once())
                        ->method('getMinParamError')
                        ->will($this->returnValue(new ParamError('LOWER_BORDER_VIOLATION')));
        $this->mockRange->expects($this->never())
                        ->method('aboveMaxBorder');
        $this->mockRange->expects($this->never())
                        ->method('getMaxParamError');
        $this->assertNull($this->rangeFilter->apply($param));
        $this->assertTrue($param->hasError('LOWER_BORDER_VIOLATION'));
    }

    /**
     * @test
     */
    public function returnsNullIfNumberAboveBorder()
    {
        $param = $this->createParam(303);
        $this->mockRange->expects($this->once())
                        ->method('belowMinBorder')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(false));
        $this->mockRange->expects($this->never())
                        ->method('getMinParamError');
        $this->mockRange->expects($this->once())
                        ->method('aboveMaxBorder')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(true));
        $this->mockRange->expects($this->once())
                        ->method('getMaxParamError')
                        ->will($this->returnValue(new ParamError('UPPER_BORDER_VIOLATION')));
        $this->assertNull($this->rangeFilter->apply($param));
        $this->assertTrue($param->hasError('UPPER_BORDER_VIOLATION'));
    }
}
?>