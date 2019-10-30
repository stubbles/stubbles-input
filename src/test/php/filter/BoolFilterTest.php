<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
/**
 * Tests for stubbles\input\ValueReader::asBool().
 *
 * @since  1.2.0
 * @group  filter
 */
class BoolFilterTest extends FilterTest
{
    /**
     * @test
     */
    public function filtering1AsIntReturnsTrue()
    {
        assertTrue($this->readParam(1)->asBool());
    }

    /**
     * @test
     */
    public function filtering1AsStringReturnsTrue()
    {
        assertTrue($this->readParam('1')->asBool());
    }

    /**
     * @test
     */
    public function filteringTrueAsStringReturnsTrue()
    {
        assertTrue($this->readParam('true')->asBool());
    }

    /**
     * @test
     */
    public function filteringTrueAsBoolReturnsTrue()
    {
        assertTrue($this->readParam(true)->asBool());
    }

    /**
     * @test
     * @since  2.4.1
     * @group  issue_49
     */
    public function filteringYesAsStringReturnsTrue()
    {
        assertTrue($this->readParam('yes')->asBool());
    }

    /**
     * @test
     */
    public function filtering0AsIntReturnsFalse()
    {
        assertFalse($this->readParam(0)->asBool());
    }

    /**
     * @test
     */
    public function filtering0AsStringReturnsFalse()
    {
        assertFalse($this->readParam('0')->asBool());
    }

    /**
     * @test
     */
    public function filteringFalseAsStringReturnsFalse()
    {
        assertFalse($this->readParam('false')->asBool());
    }

    /**
     * @test
     */
    public function filteringFalseAsBoolReturnsFalse()
    {
        assertFalse($this->readParam(false)->asBool());
    }

    /**
     * @test
     * @since  2.4.1
     * @group  issue_49
     */
    public function filteringNoAsStringReturnsFalse()
    {
        assertFalse($this->readParam('no')->asBool());
    }

    /**
     * @test
     */
    public function filteringAnyStringReturnsFalse()
    {
        assertFalse($this->readParam('a string')->asBool());
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
