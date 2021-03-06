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
use stubbles\input\filter\range\DateRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\DateFilter.
 *
 * @group  filter
 */
class DateFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @var  DateFilter
     */
    private $dateFilter;

    protected function setUp(): void
    {
        $this->dateFilter = DateFilter::instance();
        parent::setUp();
    }

    /**
     * @return  array<mixed[]>
     */
    public function getEmptyValues(): array
    {
        return [[''], [null]];
    }

    /**
     * @param  mixed  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value): void
    {
        assertNull($this->dateFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance(): void
    {
        assertThat(
                $this->dateFilter->apply($this->createParam('2008-09-27'))[0],
                equals(new Date('2008-09-27 00:00:00'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDate(): void
    {

        assertNull($this->dateFilter->apply($this->createParam('invalid date'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDate(): void
    {
        $param = $this->createParam('invalid date');
        list($_, $errors) = $this->dateFilter->apply($param);
        assertTrue(isset($errors['DATE_INVALID']));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $default = Date::now();
        assertThat(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDate(),
                equals($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asDate();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asDate();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDateReturnsValidValue(): void
    {
        $date = $this->readParam('2012-03-11')->asDate();
        assertThat(
            $date !== null ? $date->format('Y-m-d') : null,
            equals('2012-03-11')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
                $this->readParam((new Date('yesterday'))->asString())
                        ->asDate(new DateRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam((new Date('yesterday'))->asString())
             ->asDate(new DateRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
