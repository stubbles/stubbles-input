<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
 */
#[Group('core')]
class ParamRequestTest extends TestCase
{
    private ParamRequest $paramRequest;

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

    #[Test]
    public function returnsListOfParamNames(): void
    {
        assertThat(
            $this->paramRequest->paramNames(),
            equals(['foo', 'roland'])
        );
    }

    #[Test]
    public function returnsParamErrors(): void
    {
        assertThat(
            $this->paramRequest->paramErrors(),
            isInstanceOf(ParamErrors::class)
        );
    }

    #[Test]
    public function returnsFalseOnCheckForNonExistingParam(): void
    {
        assertFalse($this->paramRequest->hasParam('baz'));
    }

    #[Test]
    public function returnsTrueOnCheckForExistingParam(): void
    {
        assertTrue($this->paramRequest->hasParam('foo'));
    }

    #[Test]
    public function validateParamReturnsValueValidator(): void
    {
        assertThat(
            $this->paramRequest->validateParam('foo'),
            isInstanceOf(ValueValidator::class)
        );
    }

    #[Test]
    public function validateParamReturnsValueValidatorForNonExistingParam(): void
    {
        assertThat(
            $this->paramRequest->validateParam('baz'),
            isInstanceOf(ValueValidator::class)
        );
    }

    #[Test]
    public function readParamReturnsValueReader(): void
    {
        assertThat(
            $this->paramRequest->readParam('foo'),
            isInstanceOf(ValueReader::class)
        );
    }

    #[Test]
    public function readParamReturnsValueReaderForNonExistingParam(): void
    {
        assertThat(
            $this->paramRequest->readParam('baz'),
            isInstanceOf(ValueReader::class)
        );
    }
}
