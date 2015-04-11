<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter\range;
use stubbles\date\Date;
/**
 * Tests for stubbles\input\filter\range\DateRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DateRange
     */
    private $dateRange;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dateRange = new DateRange('2012-03-17', '2012-03-19');
    }

    /**
     * @return  array
     */
    public function outOfRangeValues()
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

    /**
     * @return  array
     */
    public function withinRangeValues()
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

    /**
     * @return  array
     */
    public function ranges()
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
        assertEquals(
                [],
                $this->dateRange->errorsOf('2012-03-17')
        );
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        assertEquals(
                ['DATE_TOO_EARLY' => ['earliestDate' => Date::castFrom('2012-03-17')->asString()]],
                $this->dateRange->errorsOf('2012-03-16')
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
    {
        assertEquals(
                ['DATE_TOO_LATE' => ['latestDate' => Date::castFrom('2012-03-19')->asString()]],
                $this->dateRange->errorsOf('2012-03-20')
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
     * @expectedException  BadMethodCallException
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsBadMethodCallException()
    {
        $this->dateRange->truncateToMaxBorder(new Date('2012-03-20'));
    }
}
