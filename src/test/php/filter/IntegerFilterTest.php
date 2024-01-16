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
use stubbles\input\filter\range\NumberRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\IntegerFilter.
 */
#[Group('filter')]
class IntegerFilterTest extends FilterTestBase
{
    public static function valueResultTuples(): Generator
    {
        yield [8, 8];
        yield ['8', 8];
        yield ['', 0];
        yield [null, null];
        yield [true, 1];
        yield [false, 0];
        yield [1.564, 1];
    }

    #[Test]
    #[DataProvider('valueResultTuples')]
    public function value(mixed $value, ?int $expected): void
    {
        $integerFilter = IntegerFilter::instance();
        assertThat(
            $integerFilter->apply($this->createParam($value))[0],
            equals($expected)
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asIntReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asInt());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asIntReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat($this->readParam(null)->defaultingTo(303)->asInt(), equals(303));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asIntReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asInt());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asIntAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asInt();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asIntReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam(4)->asInt(new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asIntAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam(4)->asInt(new NumberRange(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asIntReturnsValidValue(): void
    {
        assertThat($this->readParam('313')->asInt(), equals(313));
    }
}
