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
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\filter\range\NumberRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\FloatFilter.
 *
 * @group  filter
 */
class FloatFilterTest extends FilterTestBase
{
    public static function valueResultTuples(): Generator
    {
        yield ['8.4533', 8453];
        yield ['8.4538', 8453];
        yield ['8.45', 8450];
        yield ['8', 8000];
        yield [8.4533, 8453];
        yield [8.4538, 8453];
        yield [8.45, 8450];
        yield [8, 8000];
        yield [null, null];
        yield [true, 1000];
        yield [false, 0];
    }

    #[Test]
    #[DataProvider('valueResultTuples')]
    public function value(mixed $value, ?float $expected): void
    {
        $floatFilter = new FloatFilter();
        assertThat(
            $floatFilter->setDecimals(3)->apply($this->createParam($value))[0],
            equals($expected)
        );
    }

    #[Test]
    public function float(): void
    {
        $floatFilter = new FloatFilter();
        assertThat(
            $floatFilter->setDecimals(2)->apply($this->createParam('1.564'))[0],
            equals(156)
        );
    }

    #[Test]
    public function decimalsNotSet(): void
    {
        $floatFilter = new FloatFilter();
        assertThat($floatFilter->apply($this->createParam('1.564'))[0], equals(1.564));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asFloatReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asFloat());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asFloatReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat($this->readParam(null)->defaultingTo(3.03)->asFloat(), equals(3.03));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asFloatReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asFloat());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asFloatAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asFloat();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asFloatReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam(2.5)->asFloat(new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asFloatAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam(2.5)->asFloat(new NumberRange(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asFloatReturnsValidValue(): void
    {
        assertThat($this->readParam('3.13')->asFloat(), equals(3.13));
    }

    #[Test]
    public function asFloatReturnsValidValueUsingDecimals(): void
    {
        assertThat($this->readParam('3.13')->asFloat(null, 2), equals(313));
    }
}
