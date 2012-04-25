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
 * Tests for net\stubbles\input\filter\range\StringLength.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class StringLengthTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  StringLength
     */
    private $stringLength;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stringLength = new StringLength(1, 10);
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $stringLength = new StringLength(null, 10);
        $this->assertFalse($stringLength->belowMinBorder(''));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->stringLength->belowMinBorder(''));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueIsNull()
    {
        $this->assertTrue($this->stringLength->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->stringLength->belowMinBorder('a'));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->stringLength->belowMinBorder('ab'));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->stringLength->belowMinBorder('abcdefghijk'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $stringLength = new StringLength(0, null);
        $this->assertFalse($stringLength->aboveMaxBorder('abcdefghi'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->stringLength->aboveMaxBorder('abcdefghi'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->stringLength->aboveMaxBorder('abcdefghij'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->stringLength->aboveMaxBorder(''));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->stringLength->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->stringLength->aboveMaxBorder('abcdefghijk'));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('STRING_TOO_SHORT',
                            $this->stringLength->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('STRING_TOO_LONG',
                            $this->stringLength->getMaxParamError()->getId()
        );
    }
}
?>