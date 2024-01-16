<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use BadMethodCallException;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\range\NumberRange.
 *
 * @since  2.0.0
 */
#[Group('filter')]
#[Group('filter_range')]
class NumberRangeTest extends TestCase
{
    private NumberRange $numberRange;

    protected function setUp(): void
    {
        $this->numberRange = new NumberRange(1, 10);
    }

    public static function outOfRangeValues(): Generator
    {
        yield [0];
        yield [11];
    }

    #[Test]
    #[DataProvider('outOfRangeValues')]
    public function valueOutOfRangeIsNotContainedInRange(int $value): void
    {
        assertFalse($this->numberRange->contains($value));
    }

    public static function withinRangeValues(): Generator
    {
        yield [1];
        yield [4];
        yield [8];
        yield [10];
    }

    #[Test]
    #[DataProvider('withinRangeValues')]
    public function valueWithinRangeIsContainedInRange(int $value): void
    {
        assertTrue($this->numberRange->contains($value));
    }

    #[Test]
    public function rangeContainsLowValuesIfMinValueIsNull(): void
    {
        $numberRange = new NumberRange(null, 10);
        assertTrue($numberRange->contains(PHP_INT_MAX * -1));
    }

    #[Test]
    public function rangeContainsHighValuesIfMaxValueIsNull(): void
    {
        $numberRange = new NumberRange(1, null);
        assertTrue($numberRange->contains(PHP_INT_MAX));
    }

    public static function ranges(): Generator
    {
        yield [new NumberRange(1, 10)];
        yield [new NumberRange(null, 10)];
        yield [new NumberRange(1, null)];
    }

    #[Test]
    #[DataProvider('ranges')]
    public function rangeDoesNotContainNull(NumberRange $range): void
    {
        assertFalse($range->contains(null));
    }

    #[Test]
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->numberRange->errorsOf(3));
    }

    #[Test]
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
            $this->numberRange->errorsOf(0),
            equals(['VALUE_TOO_SMALL' => ['minNumber' => 1]])
        );
    }

    #[Test]
    public function errorListContainsMaxBorderErrorWhenValueAboveRange(): void
    {
        assertThat(
            $this->numberRange->errorsOf(11),
            equals(['VALUE_TOO_GREAT' => ['maxNumber' => 10]])
        );
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function doesNotAllowToTruncate(): void
    {
        assertFalse($this->numberRange->allowsTruncate(11));
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function tryingToTruncateThrowsMethodNotSupportedException(): void
    {
        expect(fn(): never => $this->numberRange->truncateToMaxBorder('11'))
            ->throws(BadMethodCallException::class);
    }
}
