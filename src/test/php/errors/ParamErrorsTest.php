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
use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isOfSize;
use function bovigo\assert\predicate\isSameAs;
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
        assertFalse($this->paramErrors->exist());
    }

    /**
     * @test
     */
    public function initialErrorCountIsZero()
    {
        assert($this->paramErrors, isOfSize(0));
    }

    /**
     * @test
     */
    public function paramErrorsExistIfOneAppended()
    {
        $this->paramErrors->append('foo', 'errorid');
        assertTrue($this->paramErrors->exist());
    }

    /**
     * @test
     */
    public function appendedErrorExistsForGivenParamName()
    {
        $this->paramErrors->append('foo', 'errorid');
        assertTrue($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function appendedErrorExistsForGivenParamNameAndErrorId()
    {
        $this->paramErrors->append('foo', 'errorid');
        assertTrue($this->paramErrors->existForWithId('foo', 'errorid'));
    }

    /**
     * @test
     */
    public function appendingAnErrorIncreasesErrorCount()
    {
        $this->paramErrors->append('foo', 'errorid');
        assert($this->paramErrors, isOfSize(1));
    }

    /**
     * @test
     */
    public function appendedErrorIsContainedInListForParam()
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        assert(
                $this->paramErrors->getFor('foo'),
                equals(['errorid' => $paramError])
        );
    }

    /**
     * @test
     */
    public function appendedErrorIsReturnedWhenRequested()
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        assert(
                $this->paramErrors->getForWithId('foo', 'errorid'),
                isSameAs($paramError)
        );
    }

    /**
     * @test
     */
    public function existForReturnsFalseIfNoErrorAddedBefore()
    {
        assertFalse($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function getForReturnsEmptyArrayIfNoErrorAddedBefore()
    {
        assertEmptyArray($this->paramErrors->getFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorAddedBefore()
    {
        assertFalse($this->paramErrors->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorAddedBefore()
    {
        assertNull($this->paramErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorOfThisNameAddedBefore()
    {
        $this->paramErrors->append('foo', 'errorid');
        assertFalse($this->paramErrors->existForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorOfThisNameAddedBefore()
    {
        $this->paramErrors->append('foo', 'errorid');
        assertNull($this->paramErrors->getForWithId('foo', 'baz'));
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
                assert($paramName, equals('foo'));
                assert(
                        $paramErrors,
                        equals(['id1' => $paramError1, 'id2' => $paramError2])
                );
            } else {
                assert($paramName, equals('bar'));
                assert($paramErrors, equals(['id1' => $paramError3]));
            }

            $i++;
        }
    }
}
