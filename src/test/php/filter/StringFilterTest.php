<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\filter\range\StringLength;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\StringFilter.
 */
#[Group('filter')]
class StringFilterTest extends FilterTestBase
{
    private StringFilter $stringFilter;

    protected function setUp(): void
    {
        $this->stringFilter = StringFilter::instance();
        parent::setUp();
    }

    #[Test]
    public function returnsEmptyStringWhenParamIsNull(): void
    {
        assertEmptyString($this->stringFilter->apply($this->createParam(null))[0]);
    }

    #[Test]
    public function returnsEmptyStringWhenParamIsEmptyString(): void
    {
        assertEmptyString($this->stringFilter->apply($this->createParam(''))[0]);
    }

    #[Test]
    public function removesTags(): void
    {
        assertThat(
            $this->stringFilter->apply($this->createParam("kkk<b>"))[0],
            equals("kkk")
        );
    }

    #[Test]
    public function removesSlashes(): void
    {
        assertThat(
            $this->stringFilter->apply($this->createParam("\'kkk"))[0],
            equals("'kkk")
        );
    }

    #[Test]
    public function removesCarriageReturn(): void
    {
        assertThat(
            $this->stringFilter->apply($this->createParam("cde\rkkk"))[0],
            equals("cdekkk")
        );
    }

    #[Test]
    public function removesLineBreaks(): void
    {
        assertThat(
            $this->stringFilter->apply($this->createParam("ab\ncde\nkkk"))[0],
            equals("abcdekkk")
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asStringReturnsEmptyStringIfParamIsNullAndNotRequired(): void
    {
        assertEmptyString($this->readParam(null)->asString());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat(
            $this->readParam(null)->defaultingTo('baz')->asString(),
            equals('baz')
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asStringReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asString());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asStringAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asString();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asStringReturnsNullIfParamIsInvalid(): void
    {
        assertNull(
                $this->readParam('foo')->asString(new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asStringAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asString(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asStringReturnsValidValue(): void
    {
        assertThat($this->readParam('foo')->asString(), equals('foo'));
    }
}
