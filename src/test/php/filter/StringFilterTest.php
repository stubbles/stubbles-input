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

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
        assertEmptyString($this->stringFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString()
    {
        assertEmptyString($this->stringFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function removesTags()
    {
        assert(
                $this->stringFilter->apply($this->createParam("kkk<b>")),
                equals("kkk")
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        assert(
                $this->stringFilter->apply($this->createParam("\'kkk")),
                equals("'kkk")
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        assert(
                $this->stringFilter->apply($this->createParam("cde\rkkk")),
                equals("cdekkk")
        );
    }

    /**
     * @test
     */
    public function removesLineBreaks()
    {
        assert(
                $this->stringFilter->apply($this->createParam("ab\ncde\nkkk")),
                equals("abcdekkk")
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        assertEmptyString($this->readParam(null)->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assert(
                $this->readParam(null)->defaultingTo('baz')->asString(),
                equals('baz')
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
                $this->readParam('foo')->asString(new StringLength(5, null))
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
        assert($this->readParam('foo')->asString(), equals('foo'));
    }
}
