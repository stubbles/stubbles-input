<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
/**
 * Tests for stubbles\input\validator\RegexValidator.
 *
 * @group  validator
 * @deprecated  since 3.0.0, will be removed with 4.0.0
 */
class RegexValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function getValidValues()
    {
        return [['/^([a-z]{3})$/', 'foo'],
                ['/^([a-z]{3})$/i', 'foo'],
                ['/^([a-z]{3})$/i', 'Bar']
        ];
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
        return [['/^([a-z]{3})$/', 'Bar'],
                ['/^([a-z]{3})$/', 'baz0123'],
                ['/^([a-z]{3})$/', null],
                ['/^([a-z]{3})$/i', 'baz0123'],
                ['/^([a-z]{3})$/i', null]
        ];
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
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function invalidRegex()
    {
        $regexValidator = new RegexValidator('^([a-z]{3})$');
        $regexValidator->validate('foo');
    }
}
