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
use stubbles\date\span\Month;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\MonthFilter.
 *
 * @group  filter
 * @since  2.5.1
 */
class MonthFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  MonthFilter
     */
    private $monthFilter;

    protected function setUp(): void
    {
        $this->monthFilter = MonthFilter::instance();
        parent::setUp();
    }

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
    public function emptyParamsAreReturnedAsNull($value)
    {
        assertNull($this->monthFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsMonthInstance()
    {
        assertThat(
                $this->monthFilter->apply($this->createParam('2008-09-27'))[0],
                equals(new Month(2008, 9))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidMonth()
    {
        assertNull($this->monthFilter->apply($this->createParam('invalid day'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->monthFilter->apply($param);
        assertTrue(isset($errors['MONTH_INVALID']));
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
    public function asMonthReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Month();
        assertThat(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asMonth(),
                equals($default)
        );
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asMonth());
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asMonth();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asMonth());
    }

    /**
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asMonth();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asMonthReturnsValidValue()
    {
        $month = $this->readParam('2012-03-11')->asMonth();
        assertThat(
            $month !== null ? $month->asString() : null,
            equals('2012-03')
        );

    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam((new Month())->asString())
                        ->asMonth(new DatespanRange(new Date('tomorrow'), null))
        );
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam((new Month())->asString())
                ->asMonth(new DatespanRange(new Date('tomorrow'), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
