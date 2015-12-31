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
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\BoolFilter.
 *
 * @since  1.2.0
 * @group  filter
 */
class BoolFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  BoolFilter
     */
    private $boolFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->boolFilter = BoolFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function filtering1AsIntReturnsTrue()
    {
        assertTrue($this->boolFilter->apply($this->createParam(1)));
    }

    /**
     * @test
     */
    public function filtering1AsStringReturnsTrue()
    {
        assertTrue($this->boolFilter->apply($this->createParam('1')));
    }

    /**
     * @test
     */
    public function filteringTrueAsStringReturnsTrue()
    {
        assertTrue($this->boolFilter->apply($this->createParam('true')));
    }

    /**
     * @test
     */
    public function filteringTrueAsBoolReturnsTrue()
    {
        assertTrue($this->boolFilter->apply($this->createParam(true)));
    }

    /**
     * @test
     * @since  2.4.1
     * @group  issue_49
     */
    public function filteringYesAsStringReturnsTrue()
    {
        assertTrue($this->boolFilter->apply($this->createParam('yes')));
    }

    /**
     * @test
     */
    public function filtering0AsIntReturnsFalse()
    {
        assertFalse($this->boolFilter->apply($this->createParam(0)));
    }

    /**
     * @test
     */
    public function filtering0AsStringReturnsFalse()
    {
        assertFalse($this->boolFilter->apply($this->createParam('0')));
    }

    /**
     * @test
     */
    public function filteringFalseAsStringReturnsFalse()
    {
        assertFalse($this->boolFilter->apply($this->createParam('false')));
    }

    /**
     * @test
     */
    public function filteringFalseAsBoolReturnsFalse()
    {
        assertFalse($this->boolFilter->apply($this->createParam(false)));
    }

    /**
     * @test
     * @since  2.4.1
     * @group  issue_49
     */
    public function filteringNoAsStringReturnsFalse()
    {
        assertFalse($this->boolFilter->apply($this->createParam('no')));
    }

    /**
     * @test
     */
    public function filteringAnyStringReturnsFalse()
    {
        assertFalse($this->boolFilter->apply($this->createParam('a string')));
    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolReturnsDefaultIfParamIsNullAndDefaultIsNotNull()
    {
        assertTrue($this->readParam(null)->defaultingTo(true)->asBool());
    }

    /**
     * @since  1.7.0
     * @test
     */
    public function asBoolReturnsNullIfParamIsNull()
    {
        assertNull($this->readParam(null)->asBool());
    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolWithFalseValueReturnsFalse()
    {
        assertFalse($this->readParam(0)->asBool());
    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolWithTrueValueReturnsTrue()
    {
        assertTrue($this->readParam(1)->asBool());
    }
}
