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
use net\stubbles\input\filter\range\DateRange;
use net\stubbles\lang\types\Date;
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
        parent::setUp();
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

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = Date::now();
        $this->assertEquals($default,
                            $this->createValueReader(null)
                                 ->asDate($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asDate();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asDate();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDateReturnsValidValue()
    {
        $this->assertEquals('2012-03-11',
                            $this->createValueReader('2012-03-11')
                                 ->asDate()
                                 ->format('Y-m-d')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsOutOfRange()
    {
        $this->assertNull($this->createValueReader(new Date('yesterday'))
                               ->asDate(null, new DateRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueReader(new Date('yesterday'))
             ->asDate(null, new DateRange(Date::now(), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }
}
?>