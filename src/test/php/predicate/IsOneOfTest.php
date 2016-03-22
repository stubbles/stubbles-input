<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\predicate;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
/**
 * Tests for stubbles\input\predicate\isOneOf().
 *
 * @group  predicate
 * @since  6.0.0
 */
class IsOneOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\predicate\Predicate
     */
    private $isOneOf;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isOneOf = isOneOf(['foo', 'bar']);
    }

    /**
     * @return  array
     */
    public function validValues()
    {
        return [['foo'],
                ['bar'],
                [['bar', 'foo']]
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  validValues
     */
    public function validValueEvaluatesToTrue($value)
    {
        assertTrue($this->isOneOf->test($value));
    }

    /**
     * @return  array
     */
    public function invalidValues()
    {
        return [['baz'],
                [null],
                [['bar', 'foo', 'baz']]
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  invalidValues
     */
    public function invalidValueEvaluatesToFalse($value)
    {
        assertFalse($this->isOneOf->test($value));
    }
}