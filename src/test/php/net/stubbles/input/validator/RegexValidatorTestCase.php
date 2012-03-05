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
 * Tests for net\stubbles\input\validator\RegexValidator.
 *
 * @group  validator
 */
class RegexValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function returnsRegexValue()
    {
        $regexValidator = new RegexValidator('^([a-z]{3})$');
        $this->assertEquals('^([a-z]{3})$', $regexValidator->getValue());
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return array(array('/^([a-z]{3})$/', 'foo'),
                     array('/^([a-z]{3})$/i', 'foo'),
                     array('/^([a-z]{3})$/i', 'Bar')
        );
    }

    /**
     * @param  string  $regex
     * @param  string  $value
     * @test
     * @dataProvider  getValidValues
     */
    public function validValuesValidateToTrue($regex, $value)
    {
        $regexValidator = new RegexValidator($regex);
        $this->assertTrue($regexValidator->validate($value));
    }

    /**
     * @return  array<array<scalar>>
     */
    public function getInvalidValues()
    {
        return array(array('/^([a-z]{3})$/', 'Bar'),
                     array('/^([a-z]{3})$/', 'baz0123'),
                     array('/^([a-z]{3})$/', null),
                     array('/^([a-z]{3})$/i', 'baz0123'),
                     array('/^([a-z]{3})$/i', null)
        );
    }

    /**
     * @param  string  $regex
     * @param  string  $value
     * @test
     * @dataProvider  getInvalidValues
     */
    public function invalidValueValidatesToFalse($regex, $value)
    {
        $regexValidator = new RegexValidator($regex);
        $this->assertFalse($regexValidator->validate($value));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function invalidRegex()
    {
        $regexValidator = new RegexValidator('^([a-z]{3})$');
        $regexValidator->validate('foo');
    }

    /**
     * @test
     */
    public function getCriteriaReturnsRegex()
    {
        $regexValidator = new RegexValidator('/^([a-z]{3})$/');
        $this->assertEquals(array('regex' => '/^([a-z]{3})$/'),
                            $regexValidator->getCriteria()
        );
    }
}
?>