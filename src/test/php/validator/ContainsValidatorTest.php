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
 * Tests for stubbles\input\validator\ContainsValidator.
 *
 * @group  validator
 * @deprecated  since 3.0.0, will be removed with 4.0.0
 */
class ContainsValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithObjectThrowsIllegalArgumentException()
    {
        new ContainsValidator(new \stdClass());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        return [[true, true],
                [false, false],
                [5, 5],
                [5, 55],
                [5, 'foo5'],
                [5, 'fo5o'],
                ['foo', 'foobar'],
                ['foo', 'foo']
        ];
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
        return [[true, false],
                [false, true],
                [false, new \stdClass()],
                [false, null],
                [5, 'foo'],
                [5, 6],
                [true, 5],
                [false, 0],
                [true, 'foo'],
                ['foo', 'bar']
        ];
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
