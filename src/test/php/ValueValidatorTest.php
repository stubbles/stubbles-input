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
    private function createValueValidator($value)
    {
        return new ValueValidator(new Param('bar', $value));
    }

    /**
     * @test
     */
    public function containsReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValueValidator('foo')->contains('o'));
    }

    /**
     * @test
     */
    public function containsReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValueValidator('foo')->contains('u'));
    }

    /**
     * @test
     * @since  4.3.0
     */
    public function containsAnyOfReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('foo')
                     ->containsAnyOf(['bar', 'o', 'baz'])
        );
    }

    /**
     * @test
     * @since  4.3.0
     */
    public function containsAnyOfReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse(
                $this->createValueValidator('foo')
                     ->containsAnyOf(['bar', 'baz'])
        );
    }

    /**
     * @test
     */
    public function isEqualToReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue($this->createValueValidator('foo')->isEqualTo('foo'));
    }

    /**
     * @test
     */
    public function isEqualToReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValueValidator('foo')->isEqualTo('bar'));
    }

    /**
     * @test
     */
    public function isHttpUriReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('http://example.net/')->isHttpUri()
        );
    }

    /**
     * @test
     */
    public function isHttpUriReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValueValidator('foo')->isHttpUri());
    }

    /**
     * @test
     */
    public function isExistingHttpUriReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('http://localhost/')
                     ->isExistingHttpUri()
        );
    }

    /**
     * @test
     */
    public function isExistingHttpUriReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse(
                $this->createValueValidator('foo')->isExistingHttpUri()
        );
    }

    /**
     * @test
     */
    public function isExistingHttpUriReturnsFalseIfValidatorNotSatisfiedWithNonExistingUri()
    {
        $this->assertFalse(
                $this->createValueValidator('http://foo.doesnotexist')
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
        $this->assertTrue(
                $this->createValueValidator('127.0.0.1')->isIpAddress()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpAddressReturnsTrueIfValidatorSatisfiedWithIpV6Address()
    {
        $this->assertTrue(
                $this->createValueValidator('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
                     ->isIpAddress()
        );
    }

    /**
     * @test
     */
    public function isIpAddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValueValidator('foo')->isIpAddress());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('127.0.0.1')->isIpV4Address()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValueValidator('foo')->isIpV4Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV4AddressReturnsFalseForIpV6Addresses()
    {
        $this->assertFalse(
                $this->createValueValidator('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
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
        $this->assertTrue(
                $this->createValueValidator('2001:8d8f:1fe:5:abba:dbff:fefe:7755')
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
        $this->assertFalse($this->createValueValidator('foo')->isIpV6Address());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug258
     */
    public function isIpV6AddressReturnsFalseForIpV4Addresses()
    {
        $this->assertFalse(
                $this->createValueValidator('127.0.0.1')->isIpV6Address()
        );
    }

    /**
     * @test
     */
    public function isMailAddressReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('mail@example.net')->isMailAddress()
        );
    }

    /**
     * @test
     */
    public function isMailAddressReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse($this->createValueValidator('foo')->isMailAddress());
    }

    /**
     * @test
     */
    public function isOneOfReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('foo')
                     ->isOneOf(['foo', 'bar', 'baz'])
        );
    }

    /**
     * @test
     */
    public function isOneOfReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse(
                $this->createValueValidator('foo')->isOneOf(['bar', 'baz'])
        );
    }

    /**
     * @test
     */
    public function satisfiesRegexReturnsTrueIfValidatorSatisfied()
    {
        $this->assertTrue(
                $this->createValueValidator('foo')->satisfiesRegex('/foo/')
        );
    }

    /**
     * @test
     */
    public function satisfiesRegexReturnsFalseIfValidatorNotSatisfied()
    {
        $this->assertFalse(
                $this->createValueValidator('foo')->satisfiesRegex('/bar/')
        );
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withPredicateReturnsPredicateResult()
    {
        $mockPredicate = $this->getMock('stubbles\predicate\Predicate');
        $mockPredicate->expects($this->once())
                      ->method('test')
                      ->with($this->equalTo('foo'))
                      ->will($this->returnValue(true));
        $this->assertTrue(
                $this->createValueValidator('foo')->with($mockPredicate)
        );
    }

    /**
     * @test
     */
    public function canBeCreatedAsMock()
    {
        $this->assertInstanceOf(
                'stubbles\input\ValueValidator',
                ValueValidator::forValue('bar')
        );
    }
}
