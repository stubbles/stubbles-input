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
use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Helper class for the test.
 */
class FooPredicate extends Predicate
{
    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        return 'foo' === $value;
    }
}
/**
 * Test for stubbles\input\predicate\Predicate.
 *
 * @group  predicate
 * @since  6.0.0
 */
class PredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function castFromWithPredicateReturnsInstance()
    {
        $predicate = new FooPredicate();
        assert(Predicate::castFrom($predicate), isSameAs($predicate));
    }

    /**
     * @test
     */
    public function castFromWithCallableReturnsCallablePredicate()
    {
        assert(
                Predicate::castFrom(function($value) { return 'foo' === $value; }),
                isInstanceOf(CallablePredicate::class)
        );
    }

    /**
     * @test
     */
    public function castFromWithOtherValueThrowsInvalidArgumentException()
    {
        expect(function() {
                Predicate::castFrom(new \stdClass());
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function predicateIsCallable()
    {
        $predicate = new FooPredicate();
        assertTrue($predicate('foo'));
    }

    /**
     * @test
     */
    public function andReturnsAndPredicate()
    {
        $predicate = new FooPredicate();
        assert(
                $predicate->and(function($value) { return 'foo' === $value; }),
                isInstanceOf(CallablePredicate::class)
        );
    }

    /**
     * @test
     * @since  7.1.0
     */
    public function andWithoutArgumentThrowsInvalidArgumentException()
    {
        $predicate = new FooPredicate();
        expect(function() use ($predicate) {
                $predicate->and();
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function orReturnsOrPredicate()
    {
        $predicate = new FooPredicate();
        assert(
                $predicate->or(function($value) { return 'foo' === $value; }),
                isInstanceOf(CallablePredicate::class)
        );
    }

    /**
     * @test
     * @since  7.1.0
     */
    public function orWithoutArgumentThrowsInvalidArgumentException()
    {
        $predicate = new FooPredicate();
        expect(function() use ($predicate) {
                $predicate->or();
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  7.1.0
     */
    public function callToUndefinedMethodThrowsBadMethodCallException()
    {
        $predicate = new FooPredicate();
        expect(function() use ($predicate) {
                $predicate->noWay();
        })->throws(\BadMethodCallException::class);
    }
}
