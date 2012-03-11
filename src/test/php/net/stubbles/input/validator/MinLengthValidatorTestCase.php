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
 * Tests for net\stubbles\input\validator\MinLengthValidator.
 *
 * @group  validator
 */
class MinLengthValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MinLengthValidator
     */
    protected $minLengthValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->minLengthValidator = new MinLengthValidator(5);
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return array(array('hällo'),
                     array('hällö'),
                     array('äöüßµ'),
                     array('12345'),
                     array('123456'),
                     array('1234567890')
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->minLengthValidator->validate($value));
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return array(array('123'),
                     array('1234'),
                     array('äöüß')
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->minLengthValidator->validate($value));
    }
}
?>