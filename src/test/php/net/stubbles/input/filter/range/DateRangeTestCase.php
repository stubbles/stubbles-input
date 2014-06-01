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
use stubbles\date\Date;
/**
 * Tests for net\stubbles\input\filter\range\DateRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class DateRangeTestCase extends \PHPUnit_Framework_TestCase
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
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function belowMinBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->dateRange->belowMinBorder('foo');
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $dateRange = new DateRange(null, new Date('2012-03-19'));
        $this->assertFalse($dateRange->belowMinBorder(0));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->dateRange->belowMinBorder(new Date('2012-03-16')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->dateRange->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->dateRange->belowMinBorder(new Date('2012-03-17')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->dateRange->belowMinBorder(new Date('2012-03-18')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->dateRange->belowMinBorder(new Date('2012-03-20')));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function aboveMaxBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->dateRange->aboveMaxBorder('foo');
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $dateRange = new DateRange(new Date('2012-03-17'), null);
        $this->assertFalse($dateRange->aboveMaxBorder(new Date('2012-03-18')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->dateRange->aboveMaxBorder(new Date('2012-03-18')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->dateRange->aboveMaxBorder(new Date('2012-03-19')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->dateRange->aboveMaxBorder(new Date('2012-03-16')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->dateRange->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->dateRange->aboveMaxBorder(new Date('2012-03-20')));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('DATE_TOO_EARLY',
                            $this->dateRange->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('DATE_TOO_LATE',
                            $this->dateRange->getMaxParamError()->getId()
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate()
    {
        $this->assertFalse($this->dateRange->allowsTruncate());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\MethodNotSupportedException
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException()
    {
        $this->dateRange->truncateToMaxBorder(new Date('2012-03-20'));
    }
}
