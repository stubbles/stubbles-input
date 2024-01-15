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
 * Tests for stubbles\input\filter\StringFilter.
 *
 * @group  filter
 */
class StringFilterTest extends FilterTestBase
{
    /**
     * @var  StringFilter
     */
    private $stringFilter;

    protected function setUp(): void
    {
        $this->stringFilter = StringFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsNull(): void
    {
        assertEmptyString($this->stringFilter->apply($this->createParam(null))[0]);
    }

    /**
     * @test
     */
    public function returnsEmptyStringWhenParamIsEmptyString(): void
    {
        assertEmptyString($this->stringFilter->apply($this->createParam(''))[0]);
    }

    /**
     * @test
     */
    public function removesTags(): void
    {
        assertThat(
                $this->stringFilter->apply($this->createParam("kkk<b>"))[0],
                equals("kkk")
        );
    }

    /**
     * @test
     */
    public function removesSlashes(): void
    {
        assertThat(
                $this->stringFilter->apply($this->createParam("\'kkk"))[0],
                equals("'kkk")
        );
    }

    /**
     * @test
     */
    public function removesCarriageReturn(): void
    {
        assertThat(
                $this->stringFilter->apply($this->createParam("cde\rkkk"))[0],
                equals("cdekkk")
        );
    }

    /**
     * @test
     */
    public function removesLineBreaks(): void
    {
        assertThat(
                $this->stringFilter->apply($this->createParam("ab\ncde\nkkk"))[0],
                equals("abcdekkk")
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsEmptyStringIfParamIsNullAndNotRequired(): void
    {
        assertEmptyString($this->readParam(null)->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat(
                $this->readParam(null)->defaultingTo('baz')->asString(),
                equals('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asString();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsInvalid(): void
    {
        assertNull(
                $this->readParam('foo')->asString(new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asString(new StringLength(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asStringReturnsValidValue(): void
    {
        assertThat($this->readParam('foo')->asString(), equals('foo'));
    }
}
