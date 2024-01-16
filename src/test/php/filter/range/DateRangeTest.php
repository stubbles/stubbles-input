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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\date\Date;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\range\DateRange.
 *
 * @since  2.0.0
 */
#[Group('filter')]
#[Group('filter_range')]
class DateRangeTest extends TestCase
{
    private DateRange $dateRange;

    protected function setUp(): void
    {
        $this->dateRange = new DateRange('2012-03-17', '2012-03-19');
    }

    public static function outOfRangeValues(): Generator
    {
        yield ['2012-03-16'];
        yield ['2012-03-20'];
    }

    #[Test]
    #[DataProvider('outOfRangeValues')]
    public function valueOutOfRangeIsNotContainedInRange(string $value): void
    {
        assertFalse($this->dateRange->contains($value));
    }

    public static function withinRangeValues(): Generator
    {
        yield ['2012-03-17'];
        yield ['2012-03-18'];
        yield ['2012-03-19'];
    }

    #[Test]
    #[DataProvider('withinRangeValues')]
    public function valueWithinRangeIsContainedInRange(string $value): void
    {
        assertTrue($this->dateRange->contains($value));
    }

    #[Test]
    public function rangeContainsLowValuesIfMinValueIsNull(): void
    {
        $numberRange = new DateRange(null, '2012-03-19');
        assertTrue($numberRange->contains(1));
    }

    #[Test]
    public function rangeContainsHighValuesIfMaxValueIsNull(): void
    {
        $numberRange = new DateRange('2012-03-17', null);
        assertTrue($numberRange->contains(PHP_INT_MAX));
    }

    public static function ranges(): Generator
    {
        yield [new DateRange('2012-03-17', '2012-03-19')];
        yield [new DateRange(null, '2012-03-19')];
        yield [new DateRange('2012-03-17', null)];
    }

    #[Test]
    #[DataProvider('ranges')]
    public function rangeDoesNotContainNull(DateRange $range): void
    {
        assertFalse($range->contains(null));
    }

    #[Test]
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->dateRange->errorsOf('2012-03-17'));
    }

    #[Test]
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
            $this->dateRange->errorsOf('2012-03-16'),
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
            $this->dateRange->errorsOf('2012-03-20'),
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
        assertFalse($this->dateRange->allowsTruncate('2012-03-20'));
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function tryingToTruncateThrowsBadMethodCallException(): void
    {
        expect(function() { $this->dateRange->truncateToMaxBorder('2012-03-20'); })
            ->throws(\BadMethodCallException::class);
    }
}
