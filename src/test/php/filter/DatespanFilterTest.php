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
use stubbles\date\Date;
use stubbles\date\span\Day;
use stubbles\input\filter\range\DatespanRange;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\DatespanFilter.
 *
 * @group  filter
 * @since  4.3.0
 */
class DatespanFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  \stubbles\date\span\DatespanFilter
     */
    private $datespanFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->datespanFilter = DatespanFilter::instance();
        parent::setUp();
    }

    /**
     * @return  scalar
     */
    public function getEmptyValues()
    {
        return [[''],
                [null]
        ];
    }

    /**
     * @param  scalar  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value)
    {
        $this->assertNull($this->datespanFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDayInstance()
    {
        $day = $this->datespanFilter->apply($this->createParam('2008-09-27'));
        $this->assertInstanceOf('stubbles\date\span\Day', $day);
        $date = $day->getStart();
        $this->assertEquals(2008, $date->year());
        $this->assertEquals(9, $date->month());
        $this->assertEquals(27, $date->day());
        $this->assertEquals(0, $date->hours());
        $this->assertEquals(0, $date->minutes());
        $this->assertEquals(0, $date->seconds());
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDay()
    {

        $this->assertNull($this->datespanFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        $this->datespanFilter->apply($param);
        $this->assertTrue($param->hasError('DATESPAN_INVALID'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        $this->assertEquals(
                $default,
                $this->createValueReader(null)
                        ->defaultingTo($default)
                        ->asDatespan()
        );
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asDatespan();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asDatespan();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsValidValue()
    {
        $this->assertEquals(
                '2012-03-11',
                $this->createValueReader('2012-03-11')
                        ->asDatespan()
                        ->format('Y-m-d')
        );

    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsOutOfRange()
    {
        $this->assertNull(
                $this->createValueReader('yesterday')
                        ->asDatespan(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueReader('yesterday')
             ->asDatespan(new DatespanRange(Date::now(), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }
}
