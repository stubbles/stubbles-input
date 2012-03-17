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
 * Tests for net\stubbles\input\filter\expectation\StringExpectation.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_expectation
 */
class StringExpectationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  StringExpectation
     */
    private $stringExpectation;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stringExpectation = StringExpectation::create()
                                                    ->minLength(1)
                                                    ->maxLength(10);
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $this->assertFalse(StringExpectation::create()
                                            ->maxLength(10)
                                            ->belowMinBorder('')
        );
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->stringExpectation->belowMinBorder(''));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueIsNull()
    {
        $this->assertTrue($this->stringExpectation->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->stringExpectation->belowMinBorder('a'));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->stringExpectation->belowMinBorder('ab'));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->stringExpectation->belowMinBorder('abcdefghijk'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $this->assertFalse(StringExpectation::create()
                                            ->minLength(0)
                                            ->aboveMaxBorder('abcdefghi')
        );
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->stringExpectation->aboveMaxBorder('abcdefghi'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->stringExpectation->aboveMaxBorder('abcdefghij'));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->stringExpectation->aboveMaxBorder(''));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->stringExpectation->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->stringExpectation->aboveMaxBorder('abcdefghijk'));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('STRING_TOO_SHORT',
                            $this->stringExpectation->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('STRING_TOO_LONG',
                            $this->stringExpectation->getMaxParamError()->getId()
        );
    }
}
?>