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
 * Tests for net\stubbles\input\validator\MaxNumberValidator.
 *
 * @group  validator
 */
class MaxNumberValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MaxNumberValidator
     */
    protected $maxNumberValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->maxNumberValidator = new MaxNumberValidator(5);
    }

    /**
     * @test
     */
    public function returnsMaxNumberValue()
    {
        $this->assertEquals(5, $this->maxNumberValidator->getValue());
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return array(array(3),
                     array(4),
                     array(4.99),
                     array(5)
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->maxNumberValidator->validate($value));
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return array(array(5.1),
                     array(6),
                     array(10)
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->maxNumberValidator->validate($value));
    }

    /**
     * @test
     */
    public function getCriteriaReturnsMaxNumber()
    {
        $this->assertEquals(array('maxNumber' => 5),
                            $this->maxNumberValidator->getCriteria()
        );
    }
}
?>