<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
/**
 * Helper class for the test.
 */
class TestAbstractRequest extends AbstractRequest
{
    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return 'test';
    }
}
/**
 * Tests for stubbles\input\AbstractRequest.
 *
 * @group  core
 */
class AbstractRequestTest extends \PHPUnit_Framework_TestCase
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
        $this->abstractRequest = new TestAbstractRequest(new Params(['foo' => 'bar', 'roland' => 'TB-303']));
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
        $this->assertEquals(['foo', 'roland'],
                            $this->abstractRequest->getParamNames()
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        $this->assertInstanceOf('stubbles\input\errors\ParamErrors',
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
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->abstractRequest->validateParam('foo')
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->abstractRequest->validateParam('baz')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->abstractRequest->readParam('foo')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->abstractRequest->readParam('baz')
        );
    }
}
