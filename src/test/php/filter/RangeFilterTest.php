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
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\RangeFilter.
 *
 * @group  filter
 */
class RangeFilterTest extends FilterTest
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
        $this->mockFilter  = $this->getMock('stubbles\input\Filter');
        $this->mockRange   = $this->getMock('stubbles\input\filter\range\Range');
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
    public function returnsNullIfDecoratedFilterReturnsNull()
    {
        $this->mockRange->expects($this->never())
                        ->method('contains');
        $this->mockRange->expects($this->never())
                        ->method('errorsOf');
        $this->assertNull($this->rangeFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->mockRange->expects($this->once())
                        ->method('contains')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(true));
        $this->mockRange->expects($this->never())
                        ->method('errorsOf');
        $this->assertEquals(303,
                            $this->rangeFilter->apply($this->createParam(303))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfValueNotInRange()
    {
        $param = $this->createParam(303);
        $this->mockRange->expects($this->once())
                        ->method('contains')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(false));
        $this->mockRange->expects($this->once())
                        ->method('allowsTruncate')
                        ->with($this->equalTo(303))
                        ->will($this->returnValue(false));
        $this->mockRange->expects($this->once())
                        ->method('errorsOf')
                        ->will($this->returnValue(['LOWER_BORDER_VIOLATION' => []]));
        $this->assertNull($this->rangeFilter->apply($param));
        $this->assertTrue($param->hasError('LOWER_BORDER_VIOLATION'));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function returnsTruncatedValueIfValueAboveMaxBorderAndTruncateAllowed()
    {
        $this->mockRange->expects($this->once())
                        ->method('contains')
                        ->with($this->equalTo('foobar'))
                        ->will($this->returnValue(false));
        $this->mockRange->expects($this->once())
                        ->method('allowsTruncate')
                        ->with($this->equalTo('foobar'))
                        ->will($this->returnValue(true));
        $this->mockRange->expects($this->once())
                        ->method('truncateToMaxBorder')
                        ->with($this->equalTo('foobar'))
                        ->will($this->returnValue('foo'));
        $this->mockRange->expects($this->never())
                        ->method('errorsOf');
        $this->assertEquals('foo',
                            $this->rangeFilter->apply($this->createParam('foobar'))
        );
    }
}
