<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\errors;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
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
class ParamErrorsTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  ParamErrors
     */
    private $paramErrors;

    protected function setUp(): void
    {
        $this->paramErrors = new ParamErrors();
    }

    /**
     * @test
     */
    public function hasNoErrorsInitially(): void
    {
        assertFalse($this->paramErrors->exist());
    }

    /**
     * @test
     */
    public function initialErrorCountIsZero(): void
    {
        assertThat($this->paramErrors, isOfSize(0));
    }

    /**
     * @test
     */
    public function paramErrorsExistIfOneAppended(): void
    {
        $this->paramErrors->append('foo', 'errorid');
        assertTrue($this->paramErrors->exist());
    }

    /**
     * @test
     */
    public function appendedErrorExistsForGivenParamName(): void
    {
        $this->paramErrors->append('foo', 'errorid');
        assertTrue($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function appendedErrorExistsForGivenParamNameAndErrorId(): void
    {
        $this->paramErrors->append('foo', 'errorid');
        assertTrue($this->paramErrors->existForWithId('foo', 'errorid'));
    }

    /**
     * @test
     */
    public function appendingAnErrorIncreasesErrorCount(): void
    {
        $this->paramErrors->append('foo', 'errorid');
        assertThat($this->paramErrors, isOfSize(1));
    }

    /**
     * @test
     */
    public function appendedErrorIsContainedInListForParam(): void
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        assertThat(
                $this->paramErrors->getFor('foo'),
                equals(['errorid' => $paramError])
        );
    }

    /**
     * @test
     */
    public function appendedErrorIsReturnedWhenRequested(): void
    {
        $paramError = $this->paramErrors->append('foo', 'errorid');
        assertThat(
                $this->paramErrors->getForWithId('foo', 'errorid'),
                isSameAs($paramError)
        );
    }

    /**
     * @test
     */
    public function existForReturnsFalseIfNoErrorAddedBefore(): void
    {
        assertFalse($this->paramErrors->existFor('foo'));
    }

    /**
     * @test
     */
    public function getForReturnsEmptyArrayIfNoErrorAddedBefore(): void
    {
        assertEmptyArray($this->paramErrors->getFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorAddedBefore(): void
    {
        assertFalse($this->paramErrors->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorAddedBefore(): void
    {
        assertNull($this->paramErrors->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorOfThisNameAddedBefore(): void
    {
        $this->paramErrors->append('foo', 'errorid');
        assertFalse($this->paramErrors->existForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorOfThisNameAddedBefore(): void
    {
        $this->paramErrors->append('foo', 'errorid');
        assertNull($this->paramErrors->getForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function canIterateOverParamErrors(): void
    {
        $paramError1 = $this->paramErrors->append('foo', 'id1');
        $paramError2 = $this->paramErrors->append('foo', 'id2');
        $paramError3 = $this->paramErrors->append('bar', 'id1');
        $i = 0;
        foreach ($this->paramErrors as $paramName => $paramErrors) {
            if (0 === $i) {
                assertThat($paramName, equals('foo'));
                assertThat(
                        $paramErrors,
                        equals(['id1' => $paramError1, 'id2' => $paramError2])
                );
            } else {
                assertThat($paramName, equals('bar'));
                assertThat($paramErrors, equals(['id1' => $paramError3]));
            }

            $i++;
        }
    }
}
