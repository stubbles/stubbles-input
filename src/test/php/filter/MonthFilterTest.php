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
     * @var  MonthFilter
     */
    private $monthFilter;

    protected function setUp(): void
    {
        $this->monthFilter = MonthFilter::instance();
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
        assertNull($this->monthFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsMonthInstance(): void
    {
        assertThat(
                $this->monthFilter->apply($this->createParam('2008-09-27'))[0],
                equals(new Month(2008, 9))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidMonth(): void
    {
        assertNull($this->monthFilter->apply($this->createParam('invalid day'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay(): void
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->monthFilter->apply($param);
        assertTrue(isset($errors['MONTH_INVALID']));
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
    public function asMonthReturnsDefaultIfParamIsNullAndNotRequired(): void
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
    public function asMonthReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asMonth());
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asMonth();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asMonthReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asMonth());
    }

    /**
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asMonth();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asMonthReturnsValidValue(): void
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
    public function asMonthReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
                $this->readParam((new Month())->asString())
                        ->asMonth(new DatespanRange(new Date('tomorrow'), null))
        );
    }

    /**
     * @test
     */
    public function asMonthAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam((new Month())->asString())
                ->asMonth(new DatespanRange(new Date('tomorrow'), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
