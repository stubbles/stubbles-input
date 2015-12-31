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
use stubbles\input\errors\ParamErrors;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
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
        assert(
                $this->abstractRequest->paramNames(),
                equals(['foo', 'roland'])
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        assert(
                $this->abstractRequest->paramErrors(),
                isInstanceOf(ParamErrors::class)
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
        assert(
                $this->abstractRequest->validateParam('foo'),
                isInstanceOf(ValueValidator::class)
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        assert(
                $this->abstractRequest->validateParam('baz'),
                isInstanceOf(ValueValidator::class)
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        assert(
                $this->abstractRequest->readParam('foo'),
                isInstanceOf(ValueReader::class)
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        assert(
                $this->abstractRequest->readParam('baz'),
                isInstanceOf(ValueReader::class)
        );
    }
}
