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
 * Tests for net\stubbles\input\validator\ContainsValidator.
 *
 * @group  validator
 */
class ContainsValidatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithObjectThrowsIllegalArgumentException()
    {
        new ContainsValidator(new \stdClass());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithNullThrowsIllegalArgumentException()
    {
        new ContainsValidator(null);
    }

    /**
     * returns tuples which evaluate to true
     *
     * @return  array
     */
    public function getTuplesEvaluatingToTrue()
    {
        return array(array(true, true),
                     array(false, false),
                     array(5, 5),
                     array(5, 55),
                     array(5, 'foo5'),
                     array(5, 'fo5o'),
                     array('foo', 'foobar'),
                     array('foo', 'foo')
        );
    }

    /**
     * @param  scalar  $contained
     * @param  mixed   $value
     * @test
     * @dataProvider  getTuplesEvaluatingToTrue
     */
    public function validatesToTrue($contained, $value)
    {
        $validator = new ContainsValidator($contained);
        $this->assertTrue($validator->validate($value));
    }

    /**
     * returns tuples which evaluate to false
     *
     * @return  array
     */
    public function getTuplesEvaluatingToFalse()
    {
        return array(array(true, false),
                     array(false, true),
                     array(false, new \stdClass()),
                     array(false, null),
                     array(5, 'foo'),
                     array(5, 6),
                     array(true, 5),
                     array(false, 0),
                     array(true, 'foo'),
                     array('foo', 'bar')
        );
    }

    /**
     * @param  scalar  $contained
     * @param  mixed   $value
     * @test
     * @dataProvider  getTuplesEvaluatingToFalse
     */
    public function validatesToFalse($contained, $value)
    {
        $validator = new ContainsValidator($contained);
        $this->assertFalse($validator->validate($value));
    }
}
?>