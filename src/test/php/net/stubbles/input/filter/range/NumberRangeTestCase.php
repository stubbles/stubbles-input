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
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\BoolFilter.
 *
 * @since  1.2.0
 * @group  filter
 */
class BoolFilterTestCase extends FilterTestCase
{
    /**
     * instance to test
     *
     * @type  BoolFilter
     */
    protected $boolFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->boolFilter = new BoolFilter();
    }

    /**
     * @test
     */
    public function filtering1AsIntReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->apply($this->createParam(1)));
    }

    /**
     * @test
     */
    public function filtering1AsStringReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->apply($this->createParam('1')));
    }

    /**
     * @test
     */
    public function filteringTrueAsStringReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->apply($this->createParam('true')));
    }

    /**
     * @test
     */
    public function filteringTrueAsBoolReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->apply($this->createParam(true)));
    }

    /**
     * @test
     */
    public function filtering0AsIntReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->apply($this->createParam(0)));
    }

    /**
     * @test
     */
    public function filtering0AsStringReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->apply($this->createParam('0')));
    }

    /**
     * @test
     */
    public function filteringFalseAsStringReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->apply($this->createParam('false')));
    }

    /**
     * @test
     */
    public function filteringFalseAsBoolReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->apply($this->createParam(false)));
    }

    /**
     * @test
     */
    public function filteringAnyStringReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->apply($this->createParam('a string')));
    }
}
?>