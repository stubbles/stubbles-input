<?php
declare(strict_types=1);
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

use function bovigo\assert\{
    assert,
    assertFalse,
    assertTrue,
    predicate\equals,
    predicate\isInstanceOf
};
/**
 * Tests for stubbles\input\ParamRequest.
 *
 * @group  core
 */
class ParamRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamRequest
     */
    private $paramRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramRequest = new class(
                ['foo' => 'bar', 'roland' => 'TB-303']
        ) extends ParamRequest {
            public function method(): string
            {
                return 'test';
            }
        };
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        assert(
                $this->paramRequest->paramNames(),
                equals(['foo', 'roland'])
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        assert(
                $this->paramRequest->paramErrors(),
                isInstanceOf(ParamErrors::class)
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingParam()
    {
        assertFalse($this->paramRequest->hasParam('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingParam()
    {
        assertTrue($this->paramRequest->hasParam('foo'));
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidator()
    {
        assert(
                $this->paramRequest->validateParam('foo'),
                isInstanceOf(ValueValidator::class)
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        assert(
                $this->paramRequest->validateParam('baz'),
                isInstanceOf(ValueValidator::class)
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        assert(
                $this->paramRequest->readParam('foo'),
                isInstanceOf(ValueReader::class)
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        assert(
                $this->paramRequest->readParam('baz'),
                isInstanceOf(ValueReader::class)
        );
    }
}
