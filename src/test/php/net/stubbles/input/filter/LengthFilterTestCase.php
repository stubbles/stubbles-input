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
 * Tests for net\stubbles\input\filter\LengthFilter.
 *
 * @group  filter
 */
class LengthFilterTestCase extends FilterTestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockStringFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockStringFilter = $this->getMock('net\\stubbles\\input\\filter\\StringFilter');
    }

    /**
     * creates instance to test
     *
     * @param   int  $minLength
     * @param   int  $maxLength
     * @return  LengthFilter
     */
    private function createLengthFilter($minLength = null, $maxLength = null)
    {
        return new LengthFilter($this->mockStringFilter, $minLength, $maxLength);
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
        $this->mockStringFilter->expects($this->once())
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
        $this->mockStringFilter->expects($this->once())
                               ->method('apply')
                               ->will($this->returnValue($value));
        return $param;
    }

    /**
     * @test
     */
    public function returnsEmptyStringIfDecoratedStringFilterReturnsEmptyString()
    {
        $this->assertEquals('',
                            $this->createLengthFilter()
                                 ->apply($this->createParam(''))
        );
    }

    /**
     * @test
     */
    public function returnsStringIfNoLengthRequirementsGiven()
    {
        $this->assertEquals('an arbitrary long string',
                            $this->createLengthFilter()
                                 ->apply($this->createParam('an arbitrary long string'))
        );
    }

    /**
     * @test
     */
    public function returnsStringIfItDoesNotViolateLengthRequirements()
    {
        $this->assertEquals('foo',
                            $this->createLengthFilter(2, 5)
                                 ->apply($this->createParam('foo'))
        );
    }

    /**
     * @test
     */
    public function returnsStringIfLongerThanMinLength()
    {
        $this->assertEquals('an arbitrary long string',
                            $this->createLengthFilter(10)
                                  ->apply($this->createParam('an arbitrary long string'))
        );
    }

    /**
     * @test
     */
    public function returnsStringIfEqualToMinLength()
    {
        $this->assertEquals('foo',
                            $this->createLengthFilter(3)
                                 ->apply($this->createParam('foo'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfStringShorterThanMinLength()
    {
        $this->assertNull($this->createLengthFilter(4)
                               ->apply($this->createParamWithoutMockPassing('foo'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenStringShorterThanMinLength()
    {
        $param = $this->createParamWithoutMockPassing('foo');
        $this->createLengthFilter(4)->apply($param);
        $this->assertTrue($param->hasError('STRING_TOO_SHORT'));
    }

    /**
     * @test
     */
    public function returnsStringIfShorterThanMaxLength()
    {
        $this->assertEquals('an arbitrary long string',
                            $this->createLengthFilter(null, 100)
                                 ->apply($this->createParam('an arbitrary long string'))
        );
    }

    /**
     * @test
     */
    public function returnsStringIfEqualToMaxLength()
    {
        $this->assertEquals('foo',
                            $this->createLengthFilter(null, 3)
                                 ->apply($this->createParam('foo'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfStringLongerThanMaxLength()
    {
        $this->assertNull($this->createLengthFilter(null, 2)
                               ->apply($this->createParamWithoutMockPassing('foo'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenStringLongerThanMaxLength()
    {
        $param = $this->createParamWithoutMockPassing('foo');
        $this->createLengthFilter(null, 2)->apply($param);
        $this->assertTrue($param->hasError('STRING_TOO_LONG'));
    }
}
?>