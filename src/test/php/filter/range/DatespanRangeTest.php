<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use Generator;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
 */
#[Group('filter')]
#[Group('filter_range')]
class DatespanRangeTest extends TestCase
{
    private DatespanRange $datespanRange;

    protected function setUp(): void
    {
        $this->datespanRange = new DatespanRange('2012-03-17', '2012-03-19');
    }

    public static function outOfRangeValues(): Generator
    {
        yield [new Day('2012-03-16')];
        yield [new Day('2012-03-20')];
    }

    #[Test]
    #[DataProvider('outOfRangeValues')]
    public function valueOutOfRangeIsNotContainedInRange(Day $value): void
    {
        assertFalse($this->datespanRange->contains($value));
    }

    public static function withinRangeValues(): Generator
    {
        yield [new Day('2012-03-17')];
        yield [new Day('2012-03-18')];
        yield [new Day('2012-03-19')];
    }

    #[Test]
    #[DataProvider('withinRangeValues')]
    public function valueWithinRangeIsContainedInRange(Day $value): void
    {
        assertTrue($this->datespanRange->contains($value));
    }

    #[Test]
    public function rangeContainsLowValuesIfMinValueIsNull(): void
    {
        $numberRange = new DatespanRange(null, '2012-03-19');
        assertTrue($numberRange->contains(new Month('1970-12')));
    }

    #[Test]
    public function rangeContainsHighValuesIfMaxValueIsNull(): void
    {
        $numberRange = new DatespanRange('2012-03-17', null);
        assertTrue($numberRange->contains(new Year(2037)));
    }

    public static function ranges(): Generator
    {
        yield [new DatespanRange('2012-03-17', '2012-03-19')];
        yield [new DatespanRange(null, '2012-03-19')];
        yield [new DatespanRange('2012-03-17', null)];
    }

    #[Test]
    #[DataProvider('ranges')]
    public function rangeDoesNotContainNull(DatespanRange $range): void
    {
        assertFalse($range->contains(null));
    }

    #[Test]
    #[DataProvider('ranges')]
    public function containsThrowsRuntimeExceptionWhenValueIsNoDatespan(
        DatespanRange $range
    ): void {
        expect(function() use ($range) {
            $range->contains('foo');
        })->throws(LogicException::class);
    }

    #[Test]
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->datespanRange->errorsOf(new Day('2012-03-17')));
    }

    #[Test]
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
            $this->datespanRange->errorsOf(new Day('2012-03-16')),
            equals([
                'DATE_TOO_EARLY' => [
                    'earliestDate' => Date::castFrom('2012-03-17')->asString()
                ]
            ])
        );
    }

    #[Test]
    public function errorListContainsMaxBorderErrorWhenValueAboveRange(): void
    {
        assertThat(
            $this->datespanRange->errorsOf(new Day('2012-03-20')),
            equals([
                'DATE_TOO_LATE' => [
                    'latestDate' => Date::castFrom('2012-03-19')->asString()
                ]
            ])
        );
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function doesNotAllowToTruncate(): void
    {
        assertFalse($this->datespanRange->allowsTruncate(new Day('2012-03-20')));
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function tryingToTruncateThrowsMethodNotSupportedException(): void
    {
        expect(function() { $this->datespanRange->truncateToMaxBorder('2012-03-20'); })
            ->throws(\BadMethodCallException::class);
    }
}
