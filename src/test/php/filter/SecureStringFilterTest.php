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
        assertEquals($expected, $actual->unveil());
    }

    /**
     * @test
     */
    public function returnsNullSecureStringWhenParamIsNull()
    {
        $this->assertSecureStringEquals(
                null,
                $this->secureStringFilter->apply($this->createParam(null))
        );
    }

    /**
     * @test
     */
    public function returnsNullSecureStringWhenParamIsEmptyString()
    {
        $this->assertSecureStringEquals(
                null,
                $this->secureStringFilter->apply($this->createParam(''))
        );
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
        assertNull($this->readParam(null)->asSecureString());
    }

    /**
     * @test
     */
    public function asSecureStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertSecureStringEquals(
                'baz',
                $this->readParam(null)
                     ->defaultingTo(SecureString::create('baz'))
                     ->asSecureString()
        );
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function asSecureStringThrowsLogicExceptionWhenDefaultValueNoInstanceOfSecureString()
    {
        $this->readParam(null)->defaultingTo('baz')->asSecureString()->unveil();
    }

    /**
     * @test
     */
    public function asSecureStringReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asSecureString());
    }

    /**
     * @test
     */
    public function asSecureStringAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asSecureString();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asSecureStringReturnsNullIfParamIsInvalid()
    {
        assertNull(
                $this->readParam('foo')->asSecureString(new StringLength(5, null))
        );
    }

    /**
     * @test
     */
    public function asSecureStringAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asSecureString(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asSecureStringReturnsValidValue()
    {
        assertEquals(
                'foo',
                $this->readParam('foo')->asSecureString()->unveil()
        );
    }

}
