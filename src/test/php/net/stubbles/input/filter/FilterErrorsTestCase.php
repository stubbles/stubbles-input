<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\lang\types\LocalizedString;
/**
 * Tests for net\stubbles\input\filter\FilterErrors.
 *
 * @group  filter
 */
class FilterErrorsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FilterErrors
     */
    protected $filterErrors;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->filterErrors = new FilterErrors();
    }

    /**
     * @test
     */
    public function hasNoErrorsInitially()
    {
        $this->assertFalse($this->filterErrors->exist());
        $this->assertEquals(0, $this->filterErrors->count());
        $this->assertEquals(array(), $this->filterErrors->get());
    }

    /**
     * @test
     */
    public function addErrorForSingleRequestValue()
    {
        $filterError = new FilterError('id');
        $this->assertSame($filterError,
                          $this->filterErrors->add($filterError,
                                                   'foo'
                                               )
        );

        $this->assertTrue($this->filterErrors->exist());
        $this->assertTrue($this->filterErrors->existFor('foo'));
        $this->assertTrue($this->filterErrors->existForWithId('foo', 'id'));
        $this->assertEquals(1, $this->filterErrors->count());
        $this->assertEquals(array('foo' => array('id' => $filterError)), $this->filterErrors->get());
        $this->assertEquals(array('id' => $filterError), $this->filterErrors->getFor('foo'));
        $this->assertEquals($filterError, $this->filterErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function addSameErrorForSameValueNameDoesNotResultInTwoErrorsOfSameKind()
    {
        $filterError = new FilterError('id');
        $this->assertSame($filterError,
                          $this->filterErrors->add($filterError,
                                                   'foo'
                                               )
        );
        $this->assertSame($filterError,
                          $this->filterErrors->add($filterError,
                                                   'foo'
                                               )
        );

        $this->assertTrue($this->filterErrors->exist());
        $this->assertEquals(1, $this->filterErrors->count());
        $this->assertEquals(array('foo' => array('id' => $filterError)),
                            $this->filterErrors->get()
        );
    }

    /**
     * @test
     */
    public function existForReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->filterErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function getForReturnsEmptyArrayIfNoErrorAddedBefore()
    {
        $this->assertEquals(array(), $this->filterErrors->getFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->filterErrors->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorAddedBefore()
    {
        $this->assertNull($this->filterErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorOfThisNameAddedBefore()
    {
        $filterError = new FilterError('id');
        $this->assertSame($filterError,
                          $this->filterErrors->add($filterError,
                                                   'foo'
                                               )
        );
        $this->assertFalse($this->filterErrors->existForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorOfThisNameAddedBefore()
    {
        $filterError = new FilterError('id');
        $this->assertSame($filterError,
                          $this->filterErrors->add($filterError,
                                                   'foo'
                                               )
        );
        $this->assertNull($this->filterErrors->getForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function canIterateOverParamErrors()
    {
        $filterError1 = new FilterError('id1');
        $filterError2 = new FilterError('id2');
        $this->filterErrors->add($filterError1,
                                 'foo'
                             );
        $this->filterErrors->add($filterError2,
                                 'foo'
                             );
        $this->filterErrors->add($filterError1,
                                 'bar'
                             );
        $i = 0;
        foreach ($this->filterErrors as $paramName => $paramFilterErrors) {
            if (0 === $i) {
                $this->assertEquals('foo', $paramName);
                $this->assertEquals(array('id1' => $filterError1,
                                          'id2' => $filterError2
                                    ),
                                    $paramFilterErrors
                );
            } else {
                $this->assertEquals('bar', $paramName);
                $this->assertEquals(array('id1' => $filterError1),
                                    $paramFilterErrors
                );
            }

            $i++;
        }
    }
}
?>