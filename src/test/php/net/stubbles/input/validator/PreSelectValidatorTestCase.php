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
 * Tests for net\stubbles\input\validator\PreSelectValidator.
 *
 * @group  validator
 */
class PreSelectValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  PreSelectValidator
     */
    protected $preSelectValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->preSelectValidator = new PreSelectValidator(array('foo', 'bar'));
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return array(array('foo'),
                     array('bar'),
                     array(array('bar', 'foo'))
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->preSelectValidator->validate($value));
    }

    /**
     * @return  array
     */
    public function getInvalidValues()
    {
        return array(array('baz'),
                     array(null),
                     array(array('bar', 'foo', 'baz'))
        );
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->preSelectValidator->validate($value));
    }
}
?>