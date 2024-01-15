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

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\TextFilter.
 *
 * @group  filter
 */
class TextFilterTest extends FilterTest
{
    /**
     * @var  TextFilter
     */
    private $textFilter;

    protected function setUp(): void
    {
        $this->textFilter = new TextFilter();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsNull(): void
    {
        assertEmptyString($this->textFilter->apply($this->createParam(null))[0]);
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString(): void
    {
        assertEmptyString($this->textFilter->apply($this->createParam(''))[0]);
    }

    /**
     * @return  array<mixed[]>
     */
    public static function allowedTags(): array
    {
        return [
            [[], 'this is bold and cursive and underlined with a link'],
            [['b', 'i'], 'this is <b>bold</b> and <i>cursive</i> and underlined with a link'],
            [['b', 'i', 'a'], 'this is <b>bold</b> and <i>cursive</i> and underlined with a <a href="http://example.org/">link</a>']
        ];
    }

    /**
     * @param  string[]  $allowedTags
     * @param  string    $expected
     * @test
     * @dataProvider  allowedTags
     */
    public function removesTags(array $allowedTags, string $expected): void
    {
        assertThat(
                $this->textFilter->allowTags($allowedTags)
                        ->apply($this->createParam(
                                'this is <b>bold</b> and <i>cursive</i> and <u>underlined</u> with a <a href="http://example.org/">link</a>'
                        ))[0],
                equals($expected)
        );
    }

    /**
     * @test
     */
    public function removesSlashes(): void
    {
        assertThat(
                $this->textFilter->apply($this->createParam("\'kkk"))[0],
                equals("'kkk")
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn(): void
    {
        assertThat(
                $this->textFilter->apply($this->createParam("cde\rkkk"))[0],
                equals("cdekkk")
        );
    }

    /**
     * @test
     */
    public function doesNotRemoveLineBreaks(): void
    {
        assertThat(
                $this->textFilter->apply($this->createParam("ab\ncde\nkkk"))[0],
                equals("ab\ncde\nkkk")
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsEmptyStringIfParamIsNullAndNotRequired(): void
    {
        assertEmptyString($this->readParam(null)->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat(
                $this->readParam(null)->defaultingTo('baz')->asText(),
                equals('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asText();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsInvalid(): void
    {
        assertNull(
                $this->readParam('foo')->asText(new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asText(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asTextReturnsValidValue(): void
    {
        assertThat($this->readParam('foo<b>')->asText(), equals('foo'));

    }

    /**
     * @test
     */
    public function asTextWithAllowedTagsReturnsValidValue(): void
    {
        assertThat($this->readParam('foo<b>')->asText(null, ['b']), equals('foo<b>'));
    }
}
