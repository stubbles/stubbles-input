<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
/**
 * Base class for composite validator tests.
 */
abstract class CompositeValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  CompositeValidator
     */
    protected $compositeValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->compositeValidator = $this->getTestInstance();
    }

    /**
     * creates instance to test
     *
     * @return  CompositeValidator
     */
    protected abstract function getTestInstance();

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function validateThrowsRuntimeExceptionIfNoValidatorsAddedBefore()
    {
        $this->compositeValidator->validate('foo');
    }

    /**
     * creates mocked validator instance
     *
     * @param   bool  $validateResult
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockValidatorWhichValidatesTo($validateResult)
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                      ->method('validate')
                      ->will($this->returnValue($validateResult));
        return $mockValidator;
    }

    /**
     * creates mocked validator instance
     *
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockValidatorWhichIsNeverCalled()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->never())
                      ->method('validate');
        return $mockValidator;
    }
}
?>