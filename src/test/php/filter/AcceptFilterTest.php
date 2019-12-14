<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use PHPUnit\Framework\TestCase;
use stubbles\peer\http\AcceptHeader;
use stubbles\values\Value;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmpty;
use function bovigo\assert\predicate\isOfSize;
/**
 * Tests for stubbles\input\filter\AcceptFilter.
 *
 * @since  2.0.1
 * @group  filter
 */
class AcceptFilterTest extends TestCase
{
    /**
     * apply filter on given value
     *
     * @param   string  $acceptHeader
     * @return  \stubbles\peer\http\AcceptHeader
     */
    private function apply(?string $acceptHeader): AcceptHeader
    {
        return AcceptFilter::instance()->apply(Value::of($acceptHeader))[0];
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsNull(): void
    {
        assertEmpty($this->apply(null));
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsEmpty(): void
    {
        assertEmpty($this->apply(''));
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsInvalid(): void
    {
        assertEmpty($this->apply('text/plain;q=5'));
    }

    /**
     * @test
     */
    public function returnsFilledAcceptHeaderWhenParamValueIsValid(): void
    {
        assertThat($this->apply('text/plain;q=0.5'), isOfSize(1));
    }
}
