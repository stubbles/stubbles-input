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
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\types\Date;
/**
 * Tests for net\stubbles\input\filter\expectation\DateExpectation.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_expectation
 */
class DateExpectationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DateExpectation
     */
    private $dateExpectation;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dateExpectation = DateExpectation::create()
                                                ->notBefore(new Date('2012-03-17'))
                                                ->notAfter(new Date('2012-03-19'));
    }

    /**
     * @test
     */
    public function createFromAnnotation()
    {
        $dateExpectation = DateExpectation::fromAnnotation(new Annotation('NumberAnnotation'));
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\expectation\\DateExpectation',
                                $dateExpectation
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidDefaultDateThrowsIllegalArgumentException()
    {
        $this->dateExpectation->useDefault(new \stdClass());
    }

    /**
     * @test
     */
    public function nullAsDefaultValueDoesNotThrowIllegalArgumentException()
    {
        $this->assertNull($this->dateExpectation->useDefault(null)->getDefault());
    }

    /**
     * @test
     */
    public function acceptsStringAsDefaultValue()
    {
        $this->assertEquals(new Date('2012-04-21'),
                            $this->dateExpectation->useDefault('2012-04-21')
                                                  ->getDefault()
        );
    }

    /**
     * @test
     */
    public function acceptsDateAsDefaultValue()
    {
        $this->assertEquals(new Date('2012-04-21'),
                            $this->dateExpectation->useDefault(new Date('2012-04-21'))
                                                  ->getDefault()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function belowMinBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->dateExpectation->belowMinBorder('foo');
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
        $this->assertTrue($this->dateExpectation->belowMinBorder(new Date('2012-03-16')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->dateExpectation->belowMinBorder(null));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueEqualToMinValue()
    {
        $this->assertFalse($this->dateExpectation->belowMinBorder(new Date('2012-03-17')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMinValue()
    {
        $this->assertFalse($this->dateExpectation->belowMinBorder(new Date('2012-03-18')));
    }

    /**
     * @test
     */
    public function belowMinBorderReturnsFalseIfValueGreaterThanMaxValue()
    {
        $this->assertFalse($this->dateExpectation->belowMinBorder(new Date('2012-03-20')));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function aboveMaxBorderThrowsRuntimeExceptionOnInvalidType()
    {
        $this->dateExpectation->aboveMaxBorder('foo');
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfNoMaxBorderDefined()
    {
        $this->assertFalse(DateExpectation::create()
                                          ->notBefore(new Date('2012-03-17'))
                                          ->aboveMaxBorder(new Date('2012-03-18'))
        );
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMaxValue()
    {
        $this->assertFalse($this->dateExpectation->aboveMaxBorder(new Date('2012-03-18')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueEqualToMaxValue()
    {
        $this->assertFalse($this->dateExpectation->aboveMaxBorder(new Date('2012-03-19')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueSmallerThanMinValue()
    {
        $this->assertFalse($this->dateExpectation->aboveMaxBorder(new Date('2012-03-16')));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsFalseIfValueIsNull()
    {
        $this->assertFalse($this->dateExpectation->aboveMaxBorder(null));
    }

    /**
     * @test
     */
    public function aboveMaxBorderReturnsTrueIfValueGreaterThanMaxValue()
    {
        $this->assertTrue($this->dateExpectation->aboveMaxBorder(new Date('2012-03-20')));
    }

    /**
     * @test
     */
    public function createsMinParamError()
    {
        $this->assertEquals('DATE_TOO_EARLY',
                            $this->dateExpectation->getMinParamError()->getId()
        );
    }

    /**
     * @test
     */
    public function createsMaxParamError()
    {
        $this->assertEquals('DATE_TOO_LATE',
                            $this->dateExpectation->getMaxParamError()->getId()
        );
    }
}
?>