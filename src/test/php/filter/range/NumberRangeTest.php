<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;
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
 * @group  filter
 * @group  filter_range
 */
class NumberRangeTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  NumberRange
     */
    private $numberRange;

    protected function setUp(): void
    {
        $this->numberRange = new NumberRange(1, 10);
    }

    /**
     * @return  array<int[]>
     */
    public static function outOfRangeValues(): array
    {
        return [
            [0],
            [11]
        ];
    }

    /**
     * @test
     * @dataProvider  outOfRangeValues
     */
    public function valueOutOfRangeIsNotContainedInRange(int $value): void
    {
        assertFalse($this->numberRange->contains($value));
    }

    /**
     * @return  array<int[]>
     */
    public static function withinRangeValues(): array
    {
        return [
            [1],
            [4],
            [8],
            [10]
        ];
    }

    /**
     * @test
     * @dataProvider  withinRangeValues
     */
    public function valueWithinRangeIsContainedInRange(int $value): void
    {
        assertTrue($this->numberRange->contains($value));
    }

    /**
     * @test
     */
    public function rangeContainsLowValuesIfMinValueIsNull(): void
    {
        $numberRange = new NumberRange(null, 10);
        assertTrue($numberRange->contains(PHP_INT_MAX * -1));
    }

    /**
     * @test
     */
    public function rangeContainsHighValuesIfMaxValueIsNull(): void
    {
        $numberRange = new NumberRange(1, null);
        assertTrue($numberRange->contains(PHP_INT_MAX));
    }

    /**
     * @return  array<NumberRange[]>
     */
    public static function ranges(): array
    {
        return [
            [new NumberRange(1, 10)],
            [new NumberRange(null, 10)],
            [new NumberRange(1, null)]
        ];
    }

    /**
     * @test
     * @dataProvider  ranges
     */
    public function rangeDoesNotContainNull(NumberRange $range): void
    {
        assertFalse($range->contains(null));
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->numberRange->errorsOf(3));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
                $this->numberRange->errorsOf(0),
                equals(['VALUE_TOO_SMALL' => ['minNumber' => 1]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange(): void
    {
        assertThat(
                $this->numberRange->errorsOf(11),
                equals(['VALUE_TOO_GREAT' => ['maxNumber' => 10]])
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate(): void
    {
        assertFalse($this->numberRange->allowsTruncate(11));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException(): void
    {
        expect(function() { $this->numberRange->truncateToMaxBorder('11'); })
            ->throws(\BadMethodCallException::class);
    }
}
