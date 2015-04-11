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
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\StringFilter.
 *
 * @group  filter
 */
class StringFilterTest extends FilterTest
{
    /**
     * the instance to test
     *
     * @type  StringFilter
     */
    private $stringFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->stringFilter = StringFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsNull()
    {
        assertEquals('', $this->stringFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString()
    {
        assertEquals('', $this->stringFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function removesTags()
    {
        assertEquals(
                "kkk",
                $this->stringFilter->apply($this->createParam("kkk<b>"))
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        assertEquals(
                "'kkk",
                $this->stringFilter->apply($this->createParam("\'kkk"))
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        assertEquals(
                "cdekkk",
                $this->stringFilter->apply($this->createParam("cde\rkkk"))
        );
    }

    /**
     * @test
     */
    public function removesLineBreaks()
    {
        assertEquals(
                "abcdekkk",
                $this->stringFilter->apply($this->createParam("ab\ncde\nkkk"))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        assertEquals('', $this->readParam(null)->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assertEquals(
                'baz',
                $this->readParam(null)->defaultingTo('baz')->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asString();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsInvalid()
    {
        assertNull(
                $this->readParam('foo')
                        ->asString(new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asString(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asStringReturnsValidValue()
    {
        assertEquals('foo', $this->readParam('foo')->asString());
    }
}
