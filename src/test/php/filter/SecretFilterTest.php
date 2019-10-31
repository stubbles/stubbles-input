<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\input\filter\range\StringLength;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\SecretFilter.
 *
 * @group  filter
 * @since  3.0.0
 */
class SecretFilterTest extends FilterTest
{
    /**
     * the instance to test
     *
     * @type  SecretFilter
     */
    private $secretFilter;

    protected function setUp(): void
    {
        $this->secretFilter = SecretFilter::instance();
        parent::setUp();
    }

    /**
     * @param  string  $expected
     * @param  Secret  $actual
     */
    private function assertSecretEquals(?string $expected, Secret $actual)
    {
        assertThat($actual->unveil(), equals($expected));
    }

    /**
     * @test
     */
    public function returnsNullSecretWhenParamIsNull()
    {
        $this->assertSecretEquals(
                null,
                $this->secretFilter->apply($this->createParam(null))[0]
        );
    }

    /**
     * @test
     */
    public function returnsNullSecretWhenParamIsEmptyString()
    {
        $this->assertSecretEquals(
                null,
                $this->secretFilter->apply($this->createParam(''))[0]
        );
    }

    /**
     * @test
     */
    public function asSecretReturnsNullSecretIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asSecret());
    }

    /**
     * @test
     */
    public function asSecretReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertSecretEquals(
                'baz',
                $this->readParam(null)
                     ->defaultingTo(Secret::create('baz'))
                     ->asSecret()
        );
    }

    /**
     * @test
     */
    public function asSecretThrowsLogicExceptionWhenDefaultValueNoInstanceOfSecret()
    {
        expect(function() {
                $this->readParam(null)->defaultingTo('baz')->asSecret()->unveil();
        })->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function asSecretReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asSecret());
    }

    /**
     * @test
     */
    public function asSecretAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asSecret();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asSecretReturnsNullIfParamIsInvalid()
    {
        assertNull(
                $this->readParam('foo')->asSecret(new StringLength(5, null))
        );
    }

    /**
     * @test
     */
    public function asSecretAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asSecret(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asSecretReturnsValidValue()
    {
        assertThat($this->readParam('foo')->asSecret()->unveil(), equals('foo'));
    }

}
