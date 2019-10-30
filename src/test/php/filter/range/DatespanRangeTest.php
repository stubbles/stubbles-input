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
     * @type  DatespanRange
     */
    private $datespanRange;

    protected function setUp(): void
    {
        $this->datespanRange = new DatespanRange('2012-03-17', '2012-03-19');
    }

    public function outOfRangeValues(): array
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
    public function valueOutOfRangeIsNotContainedInRange($value)
    {
        assertFalse($this->datespanRange->contains($value));
    }

    public function withinRangeValues(): array
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
    public function valueWithinRangeIsContainedInRange($value)
    {
        assertTrue($this->datespanRange->contains($value));
    }

    /**
     * @test
     */
    public function rangeContainsLowValuesIfMinValueIsNull()
    {
        $numberRange = new DatespanRange(null, '2012-03-19');
        assertTrue($numberRange->contains(new Month('1970-12')));
    }

    /**
     * @test
     */
    public function rangeContainsHighValuesIfMaxValueIsNull()
    {
        $numberRange = new DatespanRange('2012-03-17', null);
        assertTrue($numberRange->contains(new Year(2037)));
    }

    public function ranges(): array
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
    public function rangeDoesNotContainNull(DatespanRange $range)
    {
        assertFalse($range->contains(null));
    }

    /**
     * @text
     * @dataProvider  ranges
     */
    public function containsThrowsRuntimeExceptionWhenValueIsNoDatespan(DatespanRange $range)
    {
        expect(function() use ($range) {
                $range->contains('foo');
        })->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange()
    {
        assertEmptyArray($this->datespanRange->errorsOf(new Day('2012-03-17')));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        assertThat(
                $this->datespanRange->errorsOf(new Day('2012-03-16')),
                equals(['DATE_TOO_EARLY' => ['earliestDate' => Date::castFrom('2012-03-17')->asString()]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
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
    public function doesNotAllowToTruncate()
    {
        assertFalse($this->datespanRange->allowsTruncate(new Day('2012-03-20')));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException()
    {
        expect(function() {
                $this->datespanRange->truncateToMaxBorder(new Day('2012-03-20'));
        })->throws(\BadMethodCallException::class);
    }
}
