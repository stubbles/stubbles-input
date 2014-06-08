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
use stubbles\lang\SecureString;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\SecureStringFilter.
 *
 * @group  filter
 * @since  3.0.0
 */
class SecureStringFilterTest extends FilterTest
{
    /**
     * the instance to test
     *
     * @type  SecureStringFilter
     */
    private $secureStringFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->secureStringFilter = SecureStringFilter::instance();
        parent::setUp();
    }

    /**
     * @param  string        $expected
     * @param  SecureString  $actual
     */
    private function assertSecureStringEquals($expected, SecureString $actual)
    {
        $this->assertEquals($expected, $actual->unveil());
    }

    /**
     * @test
     */
    public function returnsNullSecureStringWhenParamIsNull()
    {
        $this->assertSecureStringEquals(null, $this->secureStringFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsNullSecureStringWhenParamIsEmptyString()
    {
        $this->assertSecureStringEquals(null, $this->secureStringFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function removesTags()
    {
        $this->assertSecureStringEquals(
                "kkk",
                $this->secureStringFilter->apply($this->createParam("kkk<b>"))
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        $this->assertSecureStringEquals(
                "'kkk",
                $this->secureStringFilter->apply($this->createParam("\'kkk"))
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        $this->assertSecureStringEquals(
                "cdekkk",
                $this->secureStringFilter->apply($this->createParam("cde\rkkk"))
        );
    }

    /**
     * @test
     */
    public function removesLineBreaks()
    {
        $this->assertSecureStringEquals(
                "abcdekkk",
                $this->secureStringFilter->apply($this->createParam("ab\ncde\nkkk"))
        );
    }

    /**
     * @test
     */
    public function asSecureStringReturnsNullSecureStringIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asSecureString());
    }

    /**
     * @test
     */
    public function asSecureStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertSecureStringEquals(
                'baz',
                $this->createValueReader(null)
                     ->defaultingTo(SecureString::create('baz'))
                     ->asSecureString()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function asSecureStringThrowsIllegalStateExceptionWhenDefaultValueNoInstanceOfSecureString()
    {
        $this->createValueReader(null)->defaultingTo('baz')->asSecureString()->unveil();
    }

    /**
     * @test
     */
    public function asSecureStringReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asSecureString());
    }

    /**
     * @test
     */
    public function asSecureStringAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asSecureString();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asSecureStringReturnsNullIfParamIsInvalid()
    {
        $this->assertNull(
                $this->createValueReader('foo')->asSecureString(new StringLength(5, null))
        );
    }

    /**
     * @test
     */
    public function asSecureStringAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asSecureString(new StringLength(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asSecureStringReturnsValidValue()
    {
        $this->assertEquals(
                'foo',
                $this->createValueReader('foo')->asSecureString()->unveil()
        );
    }

}
