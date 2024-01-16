<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
/**
 * Tests for stubbles\input\ValueReader::asBool().
 *
 * @since  1.2.0
 */
#[Group('filter')]
class BoolFilterTest extends FilterTestBase
{
    #[Test]
    public function filtering1AsIntReturnsTrue(): void
    {
        assertTrue($this->readParam(1)->asBool());
    }

    #[Test]
    public function filtering1AsStringReturnsTrue(): void
    {
        assertTrue($this->readParam('1')->asBool());
    }

    #[Test]
    public function filteringTrueAsStringReturnsTrue(): void
    {
        assertTrue($this->readParam('true')->asBool());
    }

    #[Test]
    public function filteringTrueAsBoolReturnsTrue(): void
    {
        assertTrue($this->readParam(true)->asBool());
    }

    /**
     * @since  2.4.1
     */
    #[Test]
    #[Group('issue_49')]
    public function filteringYesAsStringReturnsTrue(): void
    {
        assertTrue($this->readParam('yes')->asBool());
    }

    #[Test]
    public function filtering0AsIntReturnsFalse(): void
    {
        assertFalse($this->readParam(0)->asBool());
    }

    #[Test]
    public function filtering0AsStringReturnsFalse(): void
    {
        assertFalse($this->readParam('0')->asBool());
    }

    #[Test]
    public function filteringFalseAsStringReturnsFalse(): void
    {
        assertFalse($this->readParam('false')->asBool());
    }

    #[Test]
    public function filteringFalseAsBoolReturnsFalse(): void
    {
        assertFalse($this->readParam(false)->asBool());
    }

    /**
     * @since  2.4.1
     */
    #[Test]
    #[Group('issue_49')]
    public function filteringNoAsStringReturnsFalse(): void
    {
        assertFalse($this->readParam('no')->asBool());
    }

    #[Test]
    public function filteringAnyStringReturnsFalse(): void
    {
        assertFalse($this->readParam('a string')->asBool());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug266')]
    public function asBoolReturnsDefaultIfParamIsNullAndDefaultIsNotNull(): void
    {
        assertTrue($this->readParam(null)->defaultingTo(true)->asBool());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    public function asBoolReturnsNullIfParamIsNull(): void
    {
        assertNull($this->readParam(null)->asBool());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug266')]
    public function asBoolWithFalseValueReturnsFalse(): void
    {
        assertFalse($this->readParam(0)->asBool());
    }

    /**
     * @since  1.7.0
     */
    #[Test]
    #[Group('bug266')]
    public function asBoolWithTrueValueReturnsTrue(): void
    {
        assertTrue($this->readParam(1)->asBool());
    }
}
