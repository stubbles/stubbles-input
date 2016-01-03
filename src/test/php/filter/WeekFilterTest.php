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
use stubbles\date\span\Week;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\WeekFilter.
 *
 * @group  filter
 * @since  4.5.0
 */
class WeekFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\filter\WeekFilter
     */
    private $weekFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->weekFilter = WeekFilter::instance();
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
        assertNull($this->weekFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsWeekInstance()
    {
        assert(
                $this->weekFilter->apply($this->createParam('2008-W09')),
                equals(Week::fromString('2008-W09'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidMonth()
    {
        assertNull($this->weekFilter->apply($this->createParam('invalid day')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid week');
        $this->weekFilter->apply($param);
        assertTrue($param->hasError('WEEK_INVALID'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asMonth());
    }

    /**
     * @test
     */
    public function asWeekReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = Week::fromString('2015-W22');
        assert(
                $this->readParam(null)->defaultingTo($default)->asWeek(),
                equals($default)
        );
    }

    /**
     * @test
     */
    public function asWeekReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asWeek());
    }

    /**
     * @test
     */
    public function asWeekAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asWeek();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asWeekReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asWeek());
    }

    /**
     * @test
     */
    public function asWeekAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asWeek();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asWeekReturnsValidValue()
    {
        assert(
                $this->readParam('2012-W03')->asWeek()->asString(),
                equals('2012-W03')
        );

    }

    /**
     * @test
     * @group  foo
     */
    public function asWeekReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam(new Week(new Date('this monday')))
                        ->asWeek(new DatespanRange(Date::now()->change()->byDays(8), null))
        );
    }

    /**
     * @test
     */
    public function asWeekAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam(new Week(new Date('this monday')))
                ->asWeek(new DatespanRange(Date::now()->change()->byDays(8), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
