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
    public function method()
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
    public function returnsListOfParamNames()
    {
        assertEquals(
                ['foo', 'roland'],
                $this->abstractRequest->paramNames()
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        assertInstanceOf(
                'stubbles\input\errors\ParamErrors',
                $this->abstractRequest->paramErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingParam()
    {
        assertFalse($this->abstractRequest->hasParam('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingParam()
    {
        assertTrue($this->abstractRequest->hasParam('foo'));
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidator()
    {
        assertInstanceOf(
                'stubbles\input\ValueValidator',
                $this->abstractRequest->validateParam('foo')
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        assertInstanceOf(
                'stubbles\input\ValueValidator',
                $this->abstractRequest->validateParam('baz')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        assertInstanceOf(
                'stubbles\input\ValueReader',
                $this->abstractRequest->readParam('foo')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        assertInstanceOf(
                'stubbles\input\ValueReader',
                $this->abstractRequest->readParam('baz')
        );
    }
}
