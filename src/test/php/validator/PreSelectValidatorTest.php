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
 * Tests for stubbles\input\validator\PreSelectValidator.
 *
 * @group  validator
 * @deprecated  since 3.0.0, will be removed with 4.0.0
 */
class PreSelectValidatorTest extends \PHPUnit_Framework_TestCase
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
        $this->preSelectValidator = new PreSelectValidator(['foo', 'bar']);
    }

    /**
     * @return  array
     */
    public function getValidValues()
    {
        return [['foo'],
                ['bar'],
                [['bar', 'foo']]
        ];
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
        return [['baz'],
                [null],
                [['bar', 'foo', 'baz']]
        ];
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
