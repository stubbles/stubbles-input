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
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Year;
/**
 * Tests for stubbles\input\filter\range\DatespanRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class DatespanRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DatespanRange
     */
    private $datespanRange;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->datespanRange = new DatespanRange('2012-03-17', '2012-03-19');
    }

    /**
     * @return  array
     */
    public function outOfRangeValues()
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

    /**
     * @return  array
     */
    public function withinRangeValues()
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

    /**
     * @return  array
     */
    public function ranges()
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
     * @expectedException  LogicException
     */
    public function containsThrowsRuntimeExceptionWhenValueIsNoDatespan(DatespanRange $range)
    {
        $range->contains('foo');
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange()
    {
        assertEquals(
                [],
                $this->datespanRange->errorsOf(new Day('2012-03-17'))
        );
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        assertEquals(
                ['DATE_TOO_EARLY' => ['earliestDate' => Date::castFrom('2012-03-17')->asString()]],
                $this->datespanRange->errorsOf(new Day('2012-03-16'))
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
    {
        assertEquals(
                ['DATE_TOO_LATE' => ['latestDate' => Date::castFrom('2012-03-19')->asString()]],
                $this->datespanRange->errorsOf(new Day('2012-03-20'))
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
     * @expectedException  BadMethodCallException
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException()
    {
        $this->datespanRange->truncateToMaxBorder(new Day('2012-03-20'));
    }
}
