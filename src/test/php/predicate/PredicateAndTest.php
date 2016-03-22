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
 * Test for stubbles\input\predicate\Predicate->and().
 *
 * @group  predicate
 * @since  6.0.0
 */
class PredicateAndTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\predicate\Predicate
     */
    private $andPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->andPredicate = (new CallablePredicate(
                function($value) { return 'foo' === $value; }
        ))->and(
                function($value) { return 'foo' === $value; }
        );
    }

    /**
     * @test
     */
    public function returnsTrueWhenBothPredicatesReturnsTrue()
    {
        assertTrue($this->andPredicate->test('foo'));
    }

    /**
     * @test
     */
    public function returnsFalseWhenOnePredicateReturnsFalse()
    {
        assertFalse($this->andPredicate->test('baz'));
    }
}