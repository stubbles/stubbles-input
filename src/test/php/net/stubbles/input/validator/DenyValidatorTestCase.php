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
 * Tests for stubbles\input\validator\DenyValidator.
 *
 * @group  validator
 */
class DenyValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function getValues()
    {
        return [[123],
                ['1234'],
                [true],
                [null],
                [new \stdClass()]
        ];
    }

    /**
     * @param  mixed  $value
     * @test
     * @dataProvider  getValues
     */
    public function alwaysValidatesToFalse($value)
    {
        $denyValidator = new DenyValidator();
        $this->assertFalse($denyValidator->validate($value));
    }
}
