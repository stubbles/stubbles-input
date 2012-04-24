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
use org\stubbles\input\test\TestAbstractRequest;
/**
 * Tests for net\stubbles\input\AbstractRequest.
 *
 * @group  core
 */
class AbstractRequestTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AbstractRequest
     */
    private $abstractRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->abstractRequest = new TestAbstractRequest(new Params(array('foo' => 'bar', 'roland' => 'TB-303')));
    }

    /**
     * @test
     */
    public function isNotCancelledInitially()
    {
        $this->assertFalse($this->abstractRequest->isCancelled());
    }

    /**
     * @test
     */
    public function isCancelledAfterCancellation()
    {
        $this->assertTrue($this->abstractRequest->cancel()->isCancelled());
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(array('foo', 'roland'),
                            $this->abstractRequest->getParamNames()
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ParamErrors',
                                $this->abstractRequest->paramErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingParam()
    {
        $this->assertFalse($this->abstractRequest->hasParam('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingParam()
    {
        $this->assertTrue($this->abstractRequest->hasParam('foo'));
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidator()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->abstractRequest->validateParam('foo')
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->abstractRequest->validateParam('baz')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueFilter()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->abstractRequest->readParam('foo')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueFilterForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->abstractRequest->readParam('baz')
        );
    }
}
?>