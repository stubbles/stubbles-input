<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\errors;
/**
 * Tests for stubbles\input\errors\ParamErrors.
 *
 * @group  errors
 */
class ParamErrorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamErrors
     */
    private $paramErrors;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramErrors = new ParamErrors();
    }

    /**
     * @test
     */
    public function hasNoErrorsInitially()
    {
        $this->assertFalse($this->paramErrors->exist());
    }

    /**
     * @test
     */
    public function initialErrorCountIsZero()
    {
        $this->assertEquals(0, $this->paramErrors->count());
    }

    /**
     * @test
     */
    public function initialErrorListIsEmpty()
    {
        $this->assertEquals([], $this->paramErrors->asList());
    }

    /**
     * @test
     */
    public function paramErrorsExistIfOneAppended()
    {
        $this->paramErrors->append('foo', 'errorid');
        $this->assertTrue($this->paramErrors->exist());
    }

    /**
     * @test
     */
    public function appendedErrorExistsForGivenParamName()
    {
        $this->paramErrors->append('foo', 'errorid');
        $this->assertTrue($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function appendedErrorExistsForGivenParamNameAndErrorId()
    {
        $this->paramErrors->append('foo', 'errorid');
        $this->assertTrue($this->paramErrors->existForWithId('foo', 'errorid'));
    }

    /**
     * @test
     */
    public function appendingAnErrorIncreasesErrorCount()
    {
        $this->paramErrors->append('foo', 'errorid');
        $this->assertEquals(1, $this->paramErrors->count());
    }

    /**
     * @test
     */
    public function appendedErrorIsContainedInList()
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        $this->assertEquals(['foo' => ['errorid' => $paramError]],
                            $this->paramErrors->asList()
        );
    }

    /**
     * @test
     */
    public function appendedErrorIsContainedInListForParam()
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        $this->assertEquals(['errorid' => $paramError],
                            $this->paramErrors->getFor('foo')
        );
    }

    /**
     * @test
     */
    public function appendedErrorIsReturnedWhenRequested()
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        $this->assertSame($paramError,
                          $this->paramErrors->getForWithId('foo', 'errorid')
        );
    }

    /**
     * @test
     */
    public function existForReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function getForReturnsEmptyArrayIfNoErrorAddedBefore()
    {
        $this->assertEquals([], $this->paramErrors->getFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->paramErrors->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorAddedBefore()
    {
        $this->assertNull($this->paramErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorOfThisNameAddedBefore()
    {
        $this->paramErrors->append('foo', 'errorid');
        $this->assertFalse($this->paramErrors->existForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorOfThisNameAddedBefore()
    {
        $this->paramErrors->append('foo', 'errorid');
        $this->assertNull($this->paramErrors->getForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function canIterateOverParamErrors()
    {
        $paramError1 = $this->paramErrors->append('foo', 'id1');
        $paramError2 = $this->paramErrors->append('foo', 'id2');
        $paramError3 = $this->paramErrors->append('bar', 'id1');
        $i = 0;
        foreach ($this->paramErrors as $paramName => $paramErrors) {
            if (0 === $i) {
                $this->assertEquals('foo', $paramName);
                $this->assertEquals(['id1' => $paramError1,
                                     'id2' => $paramError2
                                    ],
                                    $paramErrors
                );
            } else {
                $this->assertEquals('bar', $paramName);
                $this->assertEquals(['id1' => $paramError3],
                                    $paramErrors
                );
            }

            $i++;
        }
    }
}
