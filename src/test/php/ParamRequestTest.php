<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use PHPUnit\Framework\TestCase;
use stubbles\input\errors\ParamErrors;

use function bovigo\assert\{
    assertThat,
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
class ParamRequestTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  ParamRequest
     */
    private $paramRequest;

    protected function setUp(): void
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
        assertThat(
                $this->paramRequest->paramNames(),
                equals(['foo', 'roland'])
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        assertThat(
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
        assertThat(
                $this->paramRequest->validateParam('foo'),
                isInstanceOf(ValueValidator::class)
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        assertThat(
                $this->paramRequest->validateParam('baz'),
                isInstanceOf(ValueValidator::class)
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        assertThat(
                $this->paramRequest->readParam('foo'),
                isInstanceOf(ValueReader::class)
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        assertThat(
                $this->paramRequest->readParam('baz'),
                isInstanceOf(ValueReader::class)
        );
    }
}
