<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\filter\range\SecretMinLength;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\SecretFilter.
 *
 * @since  3.0.0
 */
#[Group('filter')]
class SecretFilterTest extends FilterTestBase
{
    private SecretFilter $secretFilter;

    protected function setUp(): void
    {
        $this->secretFilter = SecretFilter::instance();
        parent::setUp();
    }

    private function assertSecretEquals(?string $expected, ?Secret $actualSecret = null): void
    {
        $actual = $actualSecret !== null ? $actualSecret->unveil() : null;
        assertThat($actual, equals($expected));
    }

    #[Test]
    public function returnsNullSecretWhenParamIsNull(): void
    {
        $this->assertSecretEquals(
            null,
            $this->secretFilter->apply($this->createParam(null))[0]
        );
    }

    #[Test]
    public function returnsNullSecretWhenParamIsEmptyString(): void
    {
        $this->assertSecretEquals(
            null,
            $this->secretFilter->apply($this->createParam(''))[0]
        );
    }

    #[Test]
    public function asSecretReturnsNullSecretIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asSecret());
    }

    #[Test]
    public function asSecretReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $this->assertSecretEquals(
            'baz',
            $this->readParam(null)
                ->defaultingTo(Secret::create('baz'))
                ->asSecret()
        );
    }

    #[Test]
    public function asSecretThrowsLogicExceptionWhenDefaultValueNoInstanceOfSecret(): void
    {
        expect(function() {
            $this->readParam(null)->defaultingTo('baz')->asSecret();
        })->throws(\LogicException::class);
    }

    #[Test]
    public function asSecretReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asSecret());
    }

    #[Test]
    public function asSecretAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asSecret();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    #[Test]
    public function asSecretReturnsNullIfParamIsInvalid(): void
    {
        assertNull(
            $this->readParam('foo')->asSecret(new SecretMinLength(5))
        );
    }

    #[Test]
    public function asSecretAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asSecret(new SecretMinLength(5));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asSecretReturnsValidValue(): void
    {
        $secret = $this->readParam('foo')->asSecret();
        assertThat($secret !== null ? $secret->unveil() : null, equals('foo'));
    }

}
