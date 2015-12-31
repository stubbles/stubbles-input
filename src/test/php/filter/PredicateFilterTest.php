<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use bovigo\callmap\NewInstance;
use stubbles\input\Param;
use stubbles\predicate\Predicate;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\PredicateFilter.
 *
 * @since  3.0.0
 * @group  filter
 */
class PredicateFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  PredicateFilter
     */
    private $predicateFilter;
    /**
     * mocked predicate
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $predicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->predicate       = NewInstance::of(Predicate::class);
        $this->predicateFilter = new PredicateFilter(
                $this->predicate,
                'ERROR',
                ['foo' => 'bar']
        );
    }

    /**
     * @test
     */
    public function returnsValueWhenPredicateEvaluatesToTrue()
    {
        $this->predicate->mapCalls(['test' => true]);
        assert(
                $this->predicateFilter->apply(new Param('example', 'Acperience')),
                equals('Acperience')
        );
    }

    /**
     * @test
     */
    public function doesNotAddErrorWhenPredicateEvaluatesToTrue()
    {
        $this->predicate->mapCalls(['test' => true]);
        $param = new Param('example', 'Acperience');
        $this->predicateFilter->apply($param);
        assertFalse($param->hasErrors());
    }

    /**
     * @test
     */
    public function returnsNullWhenPredicateEvaluatesToFalse()
    {
        $this->predicate->mapCalls(['test' => false]);
        assertNull(
                $this->predicateFilter->apply(new Param('example', 'Trancescript'))
        );
    }

    /**
     * @test
     */
    public function addsErrorWhenPredicateEvaluatesToFalse()
    {
        $this->predicate->mapCalls(['test' => false]);
        $param = new Param('example', 'Trancescript');
        $this->predicateFilter->apply($param);
        assertTrue($param->hasError('ERROR'));
    }
}
