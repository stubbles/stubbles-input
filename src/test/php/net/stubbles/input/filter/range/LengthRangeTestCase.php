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
/**
 * Tests for net\stubbles\input\filter\range\LengthRange.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class LengthRangeTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LengthRange
     */
    private $lengthRange;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->lengthRange = new LengthRange(1, 10);
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $this->assertFalse(LengthRange::maxOnly(10)->belowMinBorder(''));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->lengthRange->belowMinBorder(''));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueIsNull()
    {
        $this->assertTrue($this->lengthRange->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->lengthRange->belowMinBorder('a'));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->lengthRange->belowMinBorder('ab'));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->lengthRange->belowMinBorder('abcdefghijk'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $this->assertFalse(LengthRange::minOnly(0)->aboveMaxBorder('abcdefghi'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->lengthRange->aboveMaxBorder('abcdefghi'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->lengthRange->aboveMaxBorder('abcdefghij'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->lengthRange->aboveMaxBorder(''));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->lengthRange->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->lengthRange->aboveMaxBorder('abcdefghijk'));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('STRING_TOO_SHORT',
                            $this->lengthRange->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('STRING_TOO_LONG',
                            $this->lengthRange->getMaxParamError()->getId()
        );
    }
}
?>