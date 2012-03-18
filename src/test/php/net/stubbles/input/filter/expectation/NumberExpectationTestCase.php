<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\expectation;
/**
 * Tests for net\stubbles\input\filter\expectation\NumberExpectation.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_expectation
 */
class NumberExpectationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  NumberExpectation
     */
    private $numberExpectation;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->numberExpectation = NumberExpectation::create()
                                                    ->inRange(1, 10);
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $this->assertFalse(NumberExpectation::create()
                                            ->maxValue(10)
                                            ->belowMinBorder(0));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->numberExpectation->belowMinBorder(0));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueIsNull()
    {
        $this->assertTrue($this->numberExpectation->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->numberExpectation->belowMinBorder(1));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->numberExpectation->belowMinBorder(2));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->numberExpectation->belowMinBorder(11));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $this->assertFalse(NumberExpectation::create()->minValue(0)->aboveMaxBorder(9));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->numberExpectation->aboveMaxBorder(9));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->numberExpectation->aboveMaxBorder(10));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->numberExpectation->aboveMaxBorder(0));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->numberExpectation->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->numberExpectation->aboveMaxBorder(11));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('VALUE_TOO_SMALL',
                            $this->numberExpectation->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('VALUE_TOO_GREAT',
                            $this->numberExpectation->getMaxParamError()->getId()
        );
    }
}
?>