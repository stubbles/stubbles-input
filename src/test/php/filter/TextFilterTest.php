<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\filter\range\StringLength;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\TextFilter.
 */
#[Group('filter')]
class TextFilterTest extends FilterTestBase
{
    private TextFilter $textFilter;

    protected function setUp(): void
    {
        $this->textFilter = new TextFilter();
        parent::setUp();
    }

    #[Test]
    public function returnsEmptyStringWhenParamIsNull(): void
    {
        assertEmptyString($this->textFilter->apply($this->createParam(null))[0]);
    }

    #[Test]
    public function returnsEmptyStringWhenParamIsEmptyString(): void
    {
        assertEmptyString($this->textFilter->apply($this->createParam(''))[0]);
    }

    /**
     * @return  array<mixed[]>
     */
    public static function allowedTags(): Generator
    {
        yield [[], 'bold and cursive and underlined with link'];
        yield [
            ['b', 'i'],
            '<b>bold</b> and <i>cursive</i> and underlined with link'
        ];
        yield [
            ['b', 'i', 'a'],
            '<b>bold</b> and <i>cursive</i> and underlined with <a href="http://example.org/">link</a>'
        ];
    }

    /**
     * @param  string[]  $allowedTags
     */
    #[Test]
    #[DataProvider('allowedTags')]
    public function removesTags(array $allowedTags, string $expected): void
    {
        $value = '<b>bold</b> and <i>cursive</i> and <u>underlined</u> with <a href="http://example.org/">link</a>';
        assertThat(
            $this->textFilter->allowTags($allowedTags)
                ->apply($this->createParam($value))[0],
            equals($expected)
        );
    }

    #[Test]
    public function removesSlashes(): void
    {
        assertThat(
            $this->textFilter->apply($this->createParam("\'kkk"))[0],
            equals("'kkk")
        );
    }

    #[Test]
    public function removesCarriageReturn(): void
    {
        assertThat(
            $this->textFilter->apply($this->createParam("cde\rkkk"))[0],
            equals("cdekkk")
        );
    }

    #[Test]
    public function doesNotRemoveLineBreaks(): void
    {
        assertThat(
            $this->textFilter->apply($this->createParam("ab\ncde\nkkk"))[0],
            equals("ab\ncde\nkkk")
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asTextReturnsEmptyStringIfParamIsNullAndNotRequired(): void
    {
        assertEmptyString($this->readParam(null)->asText());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asTextReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat(
            $this->readParam(null)->defaultingTo('baz')->asText(),
            equals('baz')
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asTextReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asText());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asTextAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asText();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asTextReturnsNullIfParamIsInvalid(): void
    {
        assertNull(
            $this->readParam('foo')->asText(new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asTextAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asText(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asTextReturnsValidValue(): void
    {
        assertThat($this->readParam('foo<b>')->asText(), equals('foo'));

    }

    #[Test]
    public function asTextWithAllowedTagsReturnsValidValue(): void
    {
        assertThat($this->readParam('foo<b>')->asText(null, ['b']), equals('foo<b>'));
    }
}
