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
 * Tests for net\stubbles\input\filter\DateFilter.
 *
 * @group  filter
 */
class DateFilterTestCase extends FilterTestCase
{
    /**
     * instance to test
     *
     * @type  DateFilter
     */
    private $dateFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dateFilter = new DateFilter();
    }

    /**
     * @return  scalar
     */
    public function getEmptyValues()
    {
        return array(array(''),
                     array(null)
        );
    }

    /**
     * @param  scalar  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value)
    {
        $this->assertNull($this->dateFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        $date = $this->dateFilter->apply($this->createParam('2008-09-27'));
        $this->assertInstanceOf('net\\stubbles\\lang\\types\\Date', $date);
        $this->assertEquals(2008, $date->getYear());
        $this->assertEquals(9, $date->getMonth());
        $this->assertEquals(27, $date->getDay());
        $this->assertEquals(0, $date->getHours());
        $this->assertEquals(0, $date->getMinutes());
        $this->assertEquals(0, $date->getSeconds());
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDate()
    {

        $this->assertNull($this->dateFilter->apply($this->createParam('invalid date')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDate()
    {
        $param = $this->createParam('invalid date');
        $this->dateFilter->apply($param);
        $this->assertTrue($param->hasError('DATE_INVALID'));
    }
}
?>