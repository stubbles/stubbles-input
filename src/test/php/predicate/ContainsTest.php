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
 * Tests for stubbles\input\predicate\contains().
 *
 * @group  predicate
 * @since  6.0.0
 */
class ContainsTest extends \PHPUnit_Framework_TestCase
{
/**
     * returns tuples which evaluate to true
     *
     * @return  array
     */
    public function tuplesEvaluatingToTrue()
    {
        return [
                [null, null],
                [5, 'foo5'],
                [5, 'fo5o'],
                ['foo', 'foobar'],
                ['foo', 'foo'],
                ['foo', ['foo', 'bar', 'baz']],
                [null, ['foo', null, 'baz']]
        ];
    }

    /**
     * @param  mixed                      $needle
     * @param  string|array|\Traversable  $haystack
     * @test
     * @dataProvider  tuplesEvaluatingToTrue
     */
    public function evaluatesToTrue($needle, $haystack)
    {
        assertTrue(contains($needle)->test($haystack));
    }

    /**
     * returns tuples which evaluate to false
     *
     * @return  array
     */
    public function tuplesEvaluatingToFalse()
    {
        return [
                [5, 'foo'],
                [true, 'blub'],
                ['dummy', 'bar'],
                ['nope', ['foo', 'bar', 'baz']]
        ];
    }

    /**
     * @param  mixed                      $needle
     * @param  string|array|\Traversable  $haystack
     * @test
     * @dataProvider  tuplesEvaluatingToFalse
     */
    public function evaluatesToFalse($needle, $haystack)
    {
        assertFalse(contains($needle)->test($haystack));
    }
}
