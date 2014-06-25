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
use stubbles\input\Param;
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
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockPredicate   = $this->getMock('stubbles\predicate\Predicate');
        $this->predicateFilter = new PredicateFilter($this->mockPredicate, 'ERROR', ['foo' => 'bar']);
    }

    /**
     * @test
     */
    public function returnsValueWhenPredicateEvaluatesToTrue()
    {
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->with($this->equalTo('Acperience'))
                            ->will($this->returnValue(true));
        $this->assertEquals('Acperience',
                            $this->predicateFilter->apply(new Param('example', 'Acperience'))
        );
    }

    /**
     * @test
     */
    public function doesNotAddErrorWhenPredicateEvaluatesToTrue()
    {
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->with($this->equalTo('Acperience'))
                            ->will($this->returnValue(true));
        $param = new Param('example', 'Acperience');
        $this->predicateFilter->apply($param);
        $this->assertFalse($param->hasErrors());
    }

    /**
     * @test
     */
    public function returnsNullWhenPredicateEvaluatesToFalse()
    {
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->with($this->equalTo('Trancescript'))
                            ->will($this->returnValue(false));
        $this->assertNull($this->predicateFilter->apply(new Param('example', 'Trancescript')));
    }

    /**
     * @test
     */
    public function addsErrorWhenPredicateEvaluatesToFalse()
    {
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->with($this->equalTo('Trancescript'))
                            ->will($this->returnValue(false));
        $param = new Param('example', 'Trancescript');
        $this->predicateFilter->apply($param);
        $this->assertTrue($param->hasError('ERROR'));
    }
}
