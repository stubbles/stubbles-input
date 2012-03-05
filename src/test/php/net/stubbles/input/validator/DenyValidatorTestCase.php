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
 * Tests for net\stubbles\input\validator\DenyValidator.
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
        return array(array(123),
                     array('1234'),
                     array(true),
                     array(null),
                     array(new \stdClass())
        );
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

    /**
     * @test
     */
    public function hasNoCriteria()
    {
        $denyValidator = new DenyValidator();
        $this->assertEquals(array(), $denyValidator->getCriteria());
    }
}
?>