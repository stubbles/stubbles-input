<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;
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
 * @group  filter
 * @group  filter_range
 */
class DateRangeTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  DateRange
     */
    private $dateRange;

    protected function setUp(): void
    {
        $this->dateRange = new DateRange('2012-03-17', '2012-03-19');
    }

    public function outOfRangeValues(): array
    {
        return [
            ['2012-03-16'],
            ['2012-03-20']
        ];
    }

    /**
     * @test
     * @dataProvider  outOfRangeValues
     */
    public function valueOutOfRangeIsNotContainedInRange($value)
    {
        assertFalse($this->dateRange->contains($value));
    }

    public function withinRangeValues(): array
    {
        return [
            ['2012-03-17'],
            ['2012-03-18'],
            ['2012-03-19']
        ];
    }

    /**
     * @test
     * @dataProvider  withinRangeValues
     */
    public function valueWithinRangeIsContainedInRange($value)
    {
        assertTrue($this->dateRange->contains($value));
    }

    /**
     * @test
     */
    public function rangeContainsLowValuesIfMinValueIsNull()
    {
        $numberRange = new DateRange(null, '2012-03-19');
        assertTrue($numberRange->contains(1));
    }

    /**
     * @test
     */
    public function rangeContainsHighValuesIfMaxValueIsNull()
    {
        $numberRange = new DateRange('2012-03-17', null);
        assertTrue($numberRange->contains(PHP_INT_MAX));
    }

    public function ranges(): array
    {
        return [
            [new DateRange('2012-03-17', '2012-03-19')],
            [new DateRange(null, '2012-03-19')],
            [new DateRange('2012-03-17', null)]
        ];
    }

    /**
     * @test
     * @dataProvider  ranges
     */
    public function rangeDoesNotContainNull(DateRange $range)
    {
        assertFalse($range->contains(null));
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange()
    {
        assertEmptyArray($this->dateRange->errorsOf('2012-03-17'));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        assertThat(
                $this->dateRange->errorsOf('2012-03-16'),
                equals(['DATE_TOO_EARLY' => ['earliestDate' => Date::castFrom('2012-03-17')->asString()]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
    {
        assertThat(
                $this->dateRange->errorsOf('2012-03-20'),
                equals(['DATE_TOO_LATE' => ['latestDate' => Date::castFrom('2012-03-19')->asString()]])
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate()
    {
        assertFalse($this->dateRange->allowsTruncate('2012-03-20'));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsBadMethodCallException()
    {
        expect(function() {
                $this->dateRange->truncateToMaxBorder(new Date('2012-03-20'));
        })->throws(\BadMethodCallException::class);
    }
}
