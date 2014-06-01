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
 * Tests for stubbles\input\validator\EqualValidator.
 *
 * @group  validator
 */
class EqualValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * assure that construction works correct
     *
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithObject()
    {
        new EqualValidator(new \stdClass());
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
                [null, null],
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
        $validator = new EqualValidator($contained);
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
                ['foo', 'bar'],
                [5, new \stdClass()],
                ['foo', new \stdClass()]
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
        $validator = new EqualValidator($contained);
        $this->assertFalse($validator->validate($value));
    }
}
