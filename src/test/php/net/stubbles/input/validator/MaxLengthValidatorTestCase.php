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
 * Tests for net\stubbles\input\validator\MaxLengthValidator.
 *
 * @group  validator
 */
class MaxLengthValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MaxLengthValidator
     */
    protected $maxLengthValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->maxLengthValidator = new MaxLengthValidator(5);
    }

    /**
     * @test
     */
    public function returnsMaxLengthValue()
    {
        $this->assertEquals(5, $this->maxLengthValidator->getValue());
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return array(array('123'),
                     array('1234'),
                     array('12345'),
                     array('hällo'),
                     array('hällö'),
                     array('äöüßµ')
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->maxLengthValidator->validate($value));
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return array(array('äöüßµa'),
                     array('123456'),
                     array('1234567890')
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->maxLengthValidator->validate($value));
    }
}
?>