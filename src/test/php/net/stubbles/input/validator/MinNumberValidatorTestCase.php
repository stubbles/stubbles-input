<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
/**
 * Tests for net\stubbles\input\validator\MinNumberValidator.
 *
 * @group  validator
 */
class MinNumberValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MinNumberValidator
     */
    protected $minNumberValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->minNumberValidator = new MinNumberValidator(5);
    }

    /**
     * @test
     */
    public function returnsMinNumberValue()
    {
        $this->assertEquals(5, $this->minNumberValidator->getValue());
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return array(array(5),
                     array(5.1),
                     array(6),
                     array(10)
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->minNumberValidator->validate($value));
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return array(array(3),
                     array(4),
                     array(4.99)
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->minNumberValidator->validate($value));
    }

    /**
     * @test
     */
    public function getCriteriaReturnsMinNumber()
    {
        $this->assertEquals(array('minNumber' => 5),
                            $this->minNumberValidator->getCriteria()
        );
    }
}
?>