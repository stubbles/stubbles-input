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
use net\stubbles\input\Param;
/**
 * Tests for net\stubbles\input\filter\AcceptFilter.
 *
 * @since  2.0.1
 * @group  filter
 */
class AcceptFilterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * apply filter on given value
     *
     * @param   string  $value
     * @return  net\stubbles\peer\http\AcceptHeader
     */
    private function apply($value)
    {
        $acceptFilter = new AcceptFilter();
        return $acceptFilter->apply(new Param('Accept', $value));
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsNull()
    {
        $this->assertEquals(0, $this->apply(null)->count());
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsEmpty()
    {
        $this->assertEquals(0, $this->apply('')->count());
    }

    /**
     * @test
     */
    public function returnsEmptyAcceptHeaderWhenParamValueIsInvalid()
    {
        $this->assertEquals(0, $this->apply('text/plain;q=5')->count());
    }

    /**
     * @test
     */
    public function returnsFilledAcceptHeaderWhenParamValueIsValid()
    {
        $this->assertEquals(1, $this->apply('text/plain;q=0.5')->count());
    }
}
?>