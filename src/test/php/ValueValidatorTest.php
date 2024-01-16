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
use stubbles\peer\http\HttpUri;
use stubbles\values\Value;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Tests for stubbles\input\ValueValidator.
 *
 * @since  1.3.0
 */
#[Group('validator')]
class ValueValidatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Value::defineCheck('isExistingHttpUri', [HttpUri::class, 'exists']);
    }

    private function validate(string $value): ValueValidator
    {
        return new ValueValidator(Value::of($value));
    }

    #[Test]
    public function containsReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('foo')->contains('o'));
    }

    #[Test]
    public function containsReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->contains('u'));
    }

    /**
     * @since  4.3.0
     */
    #[Test]
    public function containsAnyOfReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('foo')->containsAnyOf(['bar', 'o', 'baz']));
    }

    /**
     * @since  4.3.0
     */
    #[Test]
    public function containsAnyOfReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->containsAnyOf(['bar', 'baz']));
    }

    #[Test]
    public function isEqualToReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('foo')->isEqualTo('foo'));
    }

    #[Test]
    public function isEqualToReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isEqualTo('bar'));
    }

    #[Test]
    public function isHttpUriReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('http://example.net/')->isHttpUri());
    }

    #[Test]
    public function isHttpUriReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isHttpUri());
    }

    #[Test]
    public function isExistingHttpUriReturnsTrueIfValidatorSatisfied(): void
    {
        Value::defineCheck('isExistingHttpUri', function(): bool { return true; });
        assertTrue($this->validate('http://localhost/')->isExistingHttpUri());
    }

    #[Test]
    public function isExistingHttpUriReturnsFalseIfValidatorNotSatisfied(): void
    {
        Value::defineCheck('isExistingHttpUri', function(): bool { return false; });
        assertFalse($this->validate('foo')->isExistingHttpUri());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV4Address(): void
    {
        assertTrue($this->validate('127.0.0.1')->isIpAddress());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV6Address(): void
    {
        assertTrue(
            $this->validate('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                ->isIpAddress()
        );
    }

    #[Test]
    public function isIpAddressReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isIpAddress());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpV4AddressReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('127.0.0.1')->isIpV4Address());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpV4AddressReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isIpV4Address());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpV4AddressReturnsFalseForIpV6Addresses(): void
    {
        assertFalse(
            $this->validate('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                ->isIpV4Address()
        );
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpV6AddressReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue(
            $this->validate('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                ->isIpV6Address()
        );
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpV6AddressReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isIpV6Address());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug258')]
    public function isIpV6AddressReturnsFalseForIpV4Addresses(): void
    {
        assertFalse($this->validate('127.0.0.1')->isIpV6Address());
    }

    #[Test]
    public function isMailAddressReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('mail@example.net')->isMailAddress());
    }

    #[Test]
    public function isMailAddressReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isMailAddress());
    }

    #[Test]
    public function isOneOfReturnsTrueIfValidatorSatisfied(): void
    {
        assertTrue($this->validate('foo')->isOneOf(['foo', 'bar', 'baz']));
    }

    #[Test]
    public function isOneOfReturnsFalseIfValidatorNotSatisfied(): void
    {
        assertFalse($this->validate('foo')->isOneOf(['bar', 'baz']));
    }

    #[Test]
    public function matchesReturnsTrueIfPatternMatchesValue(): void
    {
        assertTrue($this->validate('foo')->matches('/foo/'));
    }

    #[Test]
    public function matchesReturnsFalseIfPatternDoesNotMatchValue(): void
    {
        assertFalse($this->validate('foo')->matches('/bar/'));
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withPredicateReturnsPredicateResult(): void
    {
        assertTrue($this->validate('foo')->with(
            fn(Value $value): bool => 'foo' === $value->value()
        ));
    }

    #[Test]
    public function canBeCreatedAsMock(): void
    {
        assertThat(
                ValueValidator::forValue('bar'),
                isInstanceOf(ValueValidator::class)
        );
    }
}
