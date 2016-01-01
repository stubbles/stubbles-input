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
use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\range\NumberRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class NumberRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  NumberRange
     */
    private $numberRange;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->numberRange = new NumberRange(1, 10);
    }

    /**
     * @return  array
     */
    public function outOfRangeValues()
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
    public function valueOutOfRangeIsNotContainedInRange($value)
    {
        assertFalse($this->numberRange->contains($value));
    }

    /**
     * @return  array
     */
    public function withinRangeValues()
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
    public function valueWithinRangeIsContainedInRange($value)
    {
        assertTrue($this->numberRange->contains($value));
    }

    /**
     * @test
     */
    public function rangeContainsLowValuesIfMinValueIsNull()
    {
        $numberRange = new NumberRange(null, 10);
        assertTrue($numberRange->contains(PHP_INT_MAX * -1));
    }

    /**
     * @test
     */
    public function rangeContainsHighValuesIfMaxValueIsNull()
    {
        $numberRange = new NumberRange(1, null);
        assertTrue($numberRange->contains(PHP_INT_MAX));
    }

    /**
     * @return  array
     */
    public function ranges()
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
    public function rangeDoesNotContainNull(NumberRange $range)
    {
        assertFalse($range->contains(null));
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange()
    {
        assertEmptyArray($this->numberRange->errorsOf(3));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        assert(
                $this->numberRange->errorsOf(0),
                equals(['VALUE_TOO_SMALL' => ['minNumber' => 1]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
    {
        assert(
                $this->numberRange->errorsOf(11),
                equals(['VALUE_TOO_GREAT' => ['maxNumber' => 10]])
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate()
    {
        assertFalse($this->numberRange->allowsTruncate(11));
    }

    /**
     * @test
     * @expectedException  BadMethodCallException
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException()
    {
        $this->numberRange->truncateToMaxBorder(11);
    }
}
