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
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Day;
/**
 * Tests for net\stubbles\input\filter\expectation\DatespanExpectation.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_expectation
 */
class DatespanExpectationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DatespanExpectation
     */
    private $datespanExpectation;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->datespanExpectation = DatespanExpectation::create()
                                                        ->notBefore(new Date('2012-03-17'))
                                                        ->notAfter(new Date('2012-03-19'));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function belowMinBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->datespanExpectation->belowMinBorder('foo');
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfNoMinBorderDefined()
    {
        $this->assertFalse(DateExpectation::create()
                                          ->notAfter(new Date('2012-03-19'))
                                          ->belowMinBorder(0)
        );
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsTrueIfValueSmallerThanMinValue()
    {
        $this->assertTrue($this->datespanExpectation->belowMinBorder(new Day('2012-03-16')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->datespanExpectation->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->datespanExpectation->belowMinBorder(new Day('2012-03-17')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->datespanExpectation->belowMinBorder(new Day('2012-03-18')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->datespanExpectation->belowMinBorder(new Day('2012-03-20')));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function aboveMaxBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->datespanExpectation->aboveMaxBorder('foo');
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $this->assertFalse(DateExpectation::create()
                                          ->notBefore(new Date('2012-03-17'))
                                          ->aboveMaxBorder(new Day('2012-03-18'))
        );
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->datespanExpectation->aboveMaxBorder(new Day('2012-03-18')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->datespanExpectation->aboveMaxBorder(new Day('2012-03-19')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->datespanExpectation->aboveMaxBorder(new Day('2012-03-16')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->datespanExpectation->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->datespanExpectation->aboveMaxBorder(new Day('2012-03-20')));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('DATE_TOO_EARLY',
                            $this->datespanExpectation->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('DATE_TOO_LATE',
                            $this->datespanExpectation->getMaxParamError()->getId()
        );
    }
}
?>