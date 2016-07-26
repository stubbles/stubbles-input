<?php
declare(strict_types=1);
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
 * Tests for stubbles\input\filter\TextFilter.
 *
 * @group  filter
 */
class TextFilterTest extends FilterTest
{
    /**
     * the instance to test
     *
     * @type  TextFilter
     */
    private $textFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->textFilter = new TextFilter();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsNull()
    {
        assertEmptyString($this->textFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString()
    {
        assertEmptyString($this->textFilter->apply($this->createParam('')));
    }

    public function getAllowedTags(): array
    {
        return [[[], 'this is bold and cursive and underlined with a link'],
                [['b', 'i'], 'this is <b>bold</b> and <i>cursive</i> and underlined with a link'],
                [['b', 'i', 'a'], 'this is <b>bold</b> and <i>cursive</i> and underlined with a <a href="http://example.org/">link</a>']

        ];
    }

    /**
     * @param  string[]  $allowedTags
     * @param  string    $expected
     * @test
     * @dataProvider  getAllowedTags
     */
    public function removesTags(array $allowedTags, $expected)
    {
        assert(
                $this->textFilter->allowTags($allowedTags)
                        ->apply($this->createParam(
                                'this is <b>bold</b> and <i>cursive</i> and <u>underlined</u> with a <a href="http://example.org/">link</a>'
                        )),
                equals($expected)
        );
    }

    /**
     * @test
     */
    public function removesSlashes()
    {
        assert(
                $this->textFilter->apply($this->createParam("\'kkk")),
                equals("'kkk")
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn()
    {
        assert(
                $this->textFilter->apply($this->createParam("cde\rkkk")),
                equals("cdekkk")
        );
    }

    /**
     * @test
     */
    public function doesNotRemoveLineBreaks()
    {
        assert(
                $this->textFilter->apply($this->createParam("ab\ncde\nkkk")),
                equals("ab\ncde\nkkk")
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        assertEmptyString($this->readParam(null)->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assert(
                $this->readParam(null)->defaultingTo('baz')->asText(),
                equals('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asText();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsInvalid()
    {
        assertNull(
                $this->readParam('foo')->asText(new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asText(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asTextReturnsValidValue()
    {
        assert($this->readParam('foo<b>')->asText(), equals('foo'));

    }

    /**
     * @test
     */
    public function asTextWithAllowedTagsReturnsValidValue()
    {
        assert($this->readParam('foo<b>')->asText(null, ['b']), equals('foo<b>'));
    }
}
