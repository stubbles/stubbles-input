<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\range;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Day;
/**
 * Tests for net\stubbles\input\filter\range\DatespanRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class DatespanRangeTestCase extends \PHPUnit_Framework_TestCase
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
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function belowMinBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->datespanRange->belowMinBorder('foo');
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $datespanRange = new DatespanRange(null, new Date('2012-03-19'));
        $this->assertFalse($datespanRange->belowMinBorder(0));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->datespanRange->belowMinBorder(new Day('2012-03-16')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->datespanRange->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->datespanRange->belowMinBorder(new Day('2012-03-17')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->datespanRange->belowMinBorder(new Day('2012-03-18')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->datespanRange->belowMinBorder(new Day('2012-03-20')));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function aboveMaxBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->datespanRange->aboveMaxBorder('foo');
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $datespanRange = new DatespanRange(new Date('2012-03-17'), null);
        $this->assertFalse($datespanRange->aboveMaxBorder(new Day('2012-03-18')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->datespanRange->aboveMaxBorder(new Day('2012-03-18')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->datespanRange->aboveMaxBorder(new Day('2012-03-19')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->datespanRange->aboveMaxBorder(new Day('2012-03-16')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->datespanRange->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->datespanRange->aboveMaxBorder(new Day('2012-03-20')));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('DATE_TOO_EARLY',
                            $this->datespanRange->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('DATE_TOO_LATE',
                            $this->datespanRange->getMaxParamError()->getId()
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate()
    {
        $this->assertFalse($this->datespanRange->allowsTruncate());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\MethodNotSupportedException
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException()
    {
        $this->datespanRange->truncateToMaxBorder(new Day('2012-03-20'));
    }
}
