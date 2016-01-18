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
 * Test for stubbles\input\predicate\Predicate->or()
 *
 * @group  predicate
 * @since  6.0.0
 */
class PredicateOrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\predicate\Predicate
     */
    private $orPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->orPredicate = (new CallablePredicate(
                function($value) { return 'bar' === $value; }
        ))->or(
                function($value) { return 'foo' === $value; }
        );
    }

    /**
     * @test
     */
    public function returnsTrueWhenOnePredicateReturnsTrue()
    {
        assertTrue($this->orPredicate->test('foo'));
    }

    /**
     * @test
     */
    public function returnsFalseWhenBothPredicatesReturnsFalse()
    {
        assertFalse($this->orPredicate->test('baz'));
    }
}
