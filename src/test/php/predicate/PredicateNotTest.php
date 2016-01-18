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
 * Test for not(stubbles\input\predicate\Predicate).
 *
 * @group  predicate
 * @since  6.0.0
 */
class PredicateNotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  \stubbles\input\predicate\Predicate
     */
    private $not;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->not = not(new CallablePredicate(
                function($value) { return 'foo' === $value; }
        ));
    }

    /**
     * @test
     */
    public function falseBecomesTrue()
    {
        assertTrue($this->not->test('bar'));
    }

    /**
     * @test
     */
    public function trueBecomesFalse()
    {
        assertFalse($this->not->test('foo'));
    }
}
