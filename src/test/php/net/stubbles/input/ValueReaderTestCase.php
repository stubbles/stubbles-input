<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input;
require_once __DIR__ . '/filter/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\ValueFilter.
 *
 * @since  1.3.0
 * @group  filter
 */
class ValueFilterTestCase extends filter\FilterTestCase
{
    /**
     * @test
     */
    public function returnsNullIfParamHasErrors()
    {
        $param = new Param('bar', 'foo');
        $param->addErrorWithId('SOME_ERROR');
        $mockFilter = $this->getMock('net\\stubbles\\input\\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->with($this->equalTo($param))
                   ->will($this->returnValue('baz'));
        $this->assertNull($this->createValueReaderWithParam($param)->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function errorListContainsParamError()
    {
        $param = new Param('bar', 'foo');
        $param->addErrorWithId('SOME_ERROR');
        $mockFilter = $this->getMock('net\\stubbles\\input\\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->with($this->equalTo($param))
                   ->will($this->returnValue('baz'));
        $this->createValueReaderWithParam($param)->withFilter($mockFilter);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'SOME_ERROR'));
    }

    /**
     * @test
     */
    public function returnsValueFromFilter()
    {
        $mockFilter = $this->getMock('net\\stubbles\\input\\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->createValueReader('foo')->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function unsecure()
    {
        $this->assertEquals('a value', $this->createValueReader('a value')->unsecure());
    }

    /**
     * @test
     */
    public function canBeCreatedAsMock()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ValueReader',
                                ValueReader::forValue('bar')
        );
    }
}
?>