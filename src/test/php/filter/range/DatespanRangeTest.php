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
use stubbles\date\Date;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Year;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\range\DatespanRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class DatespanRangeTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  DatespanRange
     */
    private $datespanRange;

    protected function setUp(): void
    {
        $this->datespanRange = new DatespanRange('2012-03-17', '2012-03-19');
    }

    /**
     * @return  array<Day[]>
     */
    public static function outOfRangeValues(): array
    {
        return [
            [new Day('2012-03-16')],
            [new Day('2012-03-20')]
        ];
    }

    /**
     * @test
     * @dataProvider  outOfRangeValues
     */
    public function valueOutOfRangeIsNotContainedInRange(Day $value): void
    {
        assertFalse($this->datespanRange->contains($value));
    }

    /**
     * @return  array<Day[]>
     */
    public static function withinRangeValues(): array
    {
        return [
            [new Day('2012-03-17')],
            [new Day('2012-03-18')],
            [new Day('2012-03-19')]
        ];
    }

    /**
     * @test
     * @dataProvider  withinRangeValues
     */
    public function valueWithinRangeIsContainedInRange(Day $value): void
    {
        assertTrue($this->datespanRange->contains($value));
    }

    /**
     * @test
     */
    public function rangeContainsLowValuesIfMinValueIsNull(): void
    {
        $numberRange = new DatespanRange(null, '2012-03-19');
        assertTrue($numberRange->contains(new Month('1970-12')));
    }

    /**
     * @test
     */
    public function rangeContainsHighValuesIfMaxValueIsNull(): void
    {
        $numberRange = new DatespanRange('2012-03-17', null);
        assertTrue($numberRange->contains(new Year(2037)));
    }

    /**
     * @return  array<DatespanRange[]>
     */
    public static function ranges(): array
    {
        return [
            [new DatespanRange('2012-03-17', '2012-03-19')],
            [new DatespanRange(null, '2012-03-19')],
            [new DatespanRange('2012-03-17', null)]
        ];
    }

    /**
     * @test
     * @dataProvider  ranges
     */
    public function rangeDoesNotContainNull(DatespanRange $range): void
    {
        assertFalse($range->contains(null));
    }

    /**
     * @text
     * @dataProvider  ranges
     */
    public function containsThrowsRuntimeExceptionWhenValueIsNoDatespan(DatespanRange $range): void
    {
        expect(function() use ($range) {
                $range->contains('foo');
        })->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->datespanRange->errorsOf(new Day('2012-03-17')));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
                $this->datespanRange->errorsOf(new Day('2012-03-16')),
                equals(['DATE_TOO_EARLY' => ['earliestDate' => Date::castFrom('2012-03-17')->asString()]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange(): void
    {
        assertThat(
                $this->datespanRange->errorsOf(new Day('2012-03-20')),
                equals(['DATE_TOO_LATE' => ['latestDate' => Date::castFrom('2012-03-19')->asString()]])
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate(): void
    {
        assertFalse($this->datespanRange->allowsTruncate(new Day('2012-03-20')));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException(): void
    {
        expect(function() { $this->datespanRange->truncateToMaxBorder('2012-03-20'); })
            ->throws(\BadMethodCallException::class);
    }
}
