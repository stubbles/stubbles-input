<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\input\filter\range\StringLength;
use stubbles\lang\Secret;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
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

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->secretFilter = SecretFilter::instance();
        parent::setUp();
    }

    /**
     * @param  string  $expected
     * @param  Secret  $actual
     */
    private function assertSecretEquals($expected, Secret $actual)
    {
        assert($actual->unveil(), equals($expected));
    }

    /**
     * @test
     */
    public function returnsNullSecretWhenParamIsNull()
    {
        $this->assertSecretEquals(
                null,
                $this->secretFilter->apply($this->createParam(null))
        );
    }

    /**
     * @test
     */
    public function returnsNullSecretWhenParamIsEmptyString()
    {
        $this->assertSecretEquals(
                null,
                $this->secretFilter->apply($this->createParam(''))
        );
    }

    /**
     * @test
     */
    public function removesTags()
    {
        $this->assertSecretEquals(
                "kkk",
                $this->secretFilter->apply($this->createParam("kkk<b>"))
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        $this->assertSecretEquals(
                "'kkk",
                $this->secretFilter->apply($this->createParam("\'kkk"))
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        $this->assertSecretEquals(
                "cdekkk",
                $this->secretFilter->apply($this->createParam("cde\rkkk"))
        );
    }

    /**
     * @test
     */
    public function removesLineBreaks()
    {
        $this->assertSecretEquals(
                "abcdekkk",
                $this->secretFilter->apply($this->createParam("ab\ncde\nkkk"))
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
     * @expectedException  LogicException
     */
    public function asSecretThrowsLogicExceptionWhenDefaultValueNoInstanceOfSecret()
    {
        $this->readParam(null)->defaultingTo('baz')->asSecret()->unveil();
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
        assert($this->readParam('foo')->asSecret()->unveil(), equals('foo'));
    }

}
