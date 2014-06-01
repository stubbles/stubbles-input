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
use stubbles\input\Param;
/**
 * Tests for stubbles\input\filter\ValidatingFilter.
 *
 * @since  2.0.0
 * @group  filter
 */
class ValidatingFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ValidatingFilter
     */
    private $validatingFilter;
    /**
     * mocked validator
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockValidator    = $this->getMock('stubbles\input\Validator');
        $this->validatingFilter = new ValidatingFilter($this->mockValidator, 'ERROR', ['foo' => 'bar']);
    }

    /**
     * @test
     */
    public function returnsValueIfValidatorSuccessful()
    {
        $this->mockValidator->expects($this->once())
                            ->method('validate')
                            ->with($this->equalTo('Acperience'))
                            ->will($this->returnValue(true));
        $this->assertEquals('Acperience',
                            $this->validatingFilter->apply(new Param('example', 'Acperience'))
        );
    }

    /**
     * @test
     */
    public function doesNotAddErrorIfValidatorSuccessful()
    {
        $this->mockValidator->expects($this->once())
                            ->method('validate')
                            ->with($this->equalTo('Acperience'))
                            ->will($this->returnValue(true));
        $param = new Param('example', 'Acperience');
        $this->validatingFilter->apply($param);
        $this->assertFalse($param->hasErrors());
    }

    /**
     * @test
     */
    public function returnsNullIfValidatorFails()
    {
        $this->mockValidator->expects($this->once())
                            ->method('validate')
                            ->with($this->equalTo('Trancescript'))
                            ->will($this->returnValue(false));
        $this->assertNull($this->validatingFilter->apply(new Param('example', 'Trancescript')));
    }

    /**
     * @test
     */
    public function addsErrorIfValidatorFails()
    {
        $this->mockValidator->expects($this->once())
                            ->method('validate')
                            ->with($this->equalTo('Trancescript'))
                            ->will($this->returnValue(false));
        $param = new Param('example', 'Trancescript');
        $this->validatingFilter->apply($param);
        $this->assertTrue($param->hasError('ERROR'));
    }
}
