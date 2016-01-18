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
use bovigo\callmap\NewInstance;
use stubbles\input\predicate\Predicate;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Tests for stubbles\input\ValueValidator.
 *
 * @since  1.3.0
 * @group  validator
 */
class ValueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * helper method to create test instances
     *
     * @param   string  $value
     * @return  ValueValidator
     */
    private function validate($value)
    {
        return new ValueValidator(new Param('bar', $value));
    }

    /**
     * @test
     */
    public function containsReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('foo')->contains('o'));
    }

    /**
     * @test
     */
    public function containsReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->contains('u'));
    }

    /**
     * @test
     * @since  4.3.0
     */
    public function containsAnyOfReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('foo')->containsAnyOf(['bar', 'o', 'baz']));
    }

    /**
     * @test
     * @since  4.3.0
     */
    public function containsAnyOfReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->containsAnyOf(['bar', 'baz']));
    }

    /**
     * @test
     */
    public function isEqualToReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('foo')->isEqualTo('foo'));
    }

    /**
     * @test
     */
    public function isEqualToReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isEqualTo('bar'));
    }

    /**
     * @test
     */
    public function isHttpUriReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('http://example.net/')->isHttpUri());
    }

    /**
     * @test
     */
    public function isHttpUriReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isHttpUri());
    }

    /**
     * @test
     */
    public function isExistingHttpUriReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('http://localhost/')->isExistingHttpUri());
    }

    /**
     * @test
     */
    public function isExistingHttpUriReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isExistingHttpUri());
    }

    /**
     * @test
     */
    public function isExistingHttpUriReturnsFalseIfValidatorNotSatisfiedWithNonExistingUri()
    {
        assertFalse(
                $this->validate('http://doesnotexist')
                     ->isExistingHttpUri()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV4Address()
    {
        assertTrue($this->validate('127.0.0.1')->isIpAddress());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV6Address()
    {
        assertTrue(
                $this->validate('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                     ->isIpAddress()
        );
    }

    /**
     * @test
     */
    public function isIpAddressReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isIpAddress());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('127.0.0.1')->isIpV4Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isIpV4Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsFalseForIpV6Addresses()
    {
        assertFalse(
                $this->validate('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                     ->isIpV4Address()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsTrueIfValidatorSatisfied()
    {
        assertTrue(
                $this->validate('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                     ->isIpV6Address()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isIpV6Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsFalseForIpV4Addresses()
    {
        assertFalse($this->validate('127.0.0.1')->isIpV6Address());
    }

    /**
     * @test
     */
    public function isMailAddressReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('mail@example.net')->isMailAddress());
    }

    /**
     * @test
     */
    public function isMailAddressReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isMailAddress());
    }

    /**
     * @test
     */
    public function isOneOfReturnsTrueIfValidatorSatisfied()
    {
        assertTrue($this->validate('foo')->isOneOf(['foo', 'bar', 'baz']));
    }

    /**
     * @test
     */
    public function isOneOfReturnsFalseIfValidatorNotSatisfied()
    {
        assertFalse($this->validate('foo')->isOneOf(['bar', 'baz']));
    }

    /**
     * @test
     */
    public function matchesReturnsTrueIfPatternMatchesValue()
    {
        assertTrue($this->validate('foo')->matches('/foo/'));
    }

    /**
     * @test
     */
    public function matchesReturnsFalseIfPatternDoesNotMatchValue()
    {
        assertFalse($this->validate('foo')->matches('/bar/'));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withPredicateReturnsPredicateResult()
    {
        $predicate = NewInstance::of(Predicate::class)
                ->mapCalls(['test' => true]);
        assertTrue($this->validate('foo')->with($predicate));
    }

    /**
     * @test
     */
    public function canBeCreatedAsMock()
    {
        assert(
                ValueValidator::forValue('bar'),
                isInstanceOf(ValueValidator::class)
        );
    }
}
