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
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Tests for net\stubbles\input\filter\range\NumberRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class NumberRangeTestCase extends \PHPUnit_Framework_TestCase
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
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $numberRange = new NumberRange(null, 10);
        $this->assertFalse($numberRange->belowMinBorder(0));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->numberRange->belowMinBorder(0));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueIsNull()
    {
        $this->assertTrue($this->numberRange->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->numberRange->belowMinBorder(1));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->numberRange->belowMinBorder(2));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->numberRange->belowMinBorder(11));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $numberRange = new NumberRange(0, null);
        $this->assertFalse($numberRange->aboveMaxBorder(9));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->numberRange->aboveMaxBorder(9));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->numberRange->aboveMaxBorder(10));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->numberRange->aboveMaxBorder(0));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->numberRange->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->numberRange->aboveMaxBorder(11));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('VALUE_TOO_SMALL',
                            $this->numberRange->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('VALUE_TOO_GREAT',
                            $this->numberRange->getMaxParamError()->getId()
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowToTruncate()
    {
        $this->assertFalse($this->numberRange->allowsTruncate());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\MethodNotSupportedException
     * @since  2.3.1
     * @group  issue41
     */
    public function tryingToTruncateThrowsMethodNotSupportedException()
    {
        $this->numberRange->truncateToMaxBorder(11);
    }
}
