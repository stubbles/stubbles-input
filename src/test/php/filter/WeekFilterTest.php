<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\date\Date;
use stubbles\date\span\Week;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\WeekFilter.
 *
 * @group  filter
 * @since  4.5.0
 */
class WeekFilterTest extends FilterTest
{
    /**
     * @var  \stubbles\input\filter\WeekFilter
     */
    private $weekFilter;

    protected function setUp(): void
    {
        $this->weekFilter = WeekFilter::instance();
        parent::setUp();
    }

    /**
     * @return  array<mixed[]>
     */
    public function getEmptyValues(): array
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
    public function emptyParamsAreReturnedAsNull($value): void
    {
        assertNull($this->weekFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsWeekInstance(): void
    {
        assertThat(
                $this->weekFilter->apply($this->createParam('2008-W09'))[0],
                equals(Week::fromString('2008-W09'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidMonth(): void
    {
        assertNull($this->weekFilter->apply($this->createParam('invalid day'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay(): void
    {
        $param = $this->createParam('invalid week');
        list($_, $errors) = $this->weekFilter->apply($param);
        assertTrue(isset($errors['WEEK_INVALID']));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asMonth());
    }

    /**
     * @test
     */
    public function asWeekReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $default = Week::fromString('2015-W22');
        assertThat(
                $this->readParam(null)->defaultingTo($default)->asWeek(),
                equals($default)
        );
    }

    /**
     * @test
     */
    public function asWeekReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asWeek());
    }

    /**
     * @test
     */
    public function asWeekAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asWeek();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asWeekReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asWeek());
    }

    /**
     * @test
     */
    public function asWeekAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asWeek();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asWeekReturnsValidValue(): void
    {
        $week = $this->readParam('2012-W03')->asWeek();
        assertThat(
            $week !== null ? $week->asString() : null,
            equals('2012-W03')
        );

    }

    /**
     * @test
     */
    public function asWeekReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
                $this->readParam((new Week(new Date('this monday')))->asString())
                        ->asWeek(new DatespanRange(Date::now()->change()->byDays(8), null))
        );
    }

    /**
     * @test
     */
    public function asWeekAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam((new Week(new Date('this monday')))->asString())
                ->asWeek(new DatespanRange(Date::now()->change()->byDays(8), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
