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

use function bovigo\assert\assert;
use function bovigo\assert\assertEmpty;
use function bovigo\assert\predicate\isOfSize;
/**
 * Tests for stubbles\input\filter\AcceptFilter.
 *
 * @since  2.0.1
 * @group  filter
 */
class AcceptFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * apply filter on given value
     *
     * @param   string  $value
     * @return  stubbles\peer\http\AcceptHeader
     */
    private function apply($value)
    {
        $acceptFilter = AcceptFilter::instance();
        return $acceptFilter->apply(new Param('Accept', $value));
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsNull()
    {
        assertEmpty($this->apply(null));
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsEmpty()
    {
        assertEmpty($this->apply(''));
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsInvalid()
    {
        assertEmpty($this->apply('text/plain;q=5'));
    }

    /**
     * @test
     */
    public function returnsFilledAcceptHeaderWhenParamValueIsValid()
    {
        assert($this->apply('text/plain;q=0.5'), isOfSize(1));
    }
}
