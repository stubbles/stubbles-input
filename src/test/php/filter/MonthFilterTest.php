<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
 * @since  2.5.1
 */
#[Group('filter')]
class MonthFilterTest extends FilterTestBase
{
    private MonthFilter $monthFilter;

    protected function setUp(): void
    {
        $this->monthFilter = MonthFilter::instance();
        parent::setUp();
    }

    #[Test]
    #[DataProviderExternal(EmptyValuesDataProvider::class, 'provideStrings')]
    public function emptyParamsAreReturnedAsNull(?string $value): void
    {
        assertNull($this->monthFilter->apply($this->createParam($value))[0]);
    }

    #[Test]
    public function validParamsAreReturnedAsMonthInstance(): void
    {
        assertThat(
            $this->monthFilter->apply($this->createParam('2008-09-27'))[0],
            equals(new Month(2008, 9))
        );
    }

    #[Test]
    public function applyReturnsNullForInvalidMonth(): void
    {
        assertNull($this->monthFilter->apply($this->createParam('invalid day'))[0]);
    }

    #[Test]
    public function applyAddsErrorForInvalidDay(): void
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->monthFilter->apply($param);
        assertTrue(isset($errors['MONTH_INVALID']));
    }

    #[Test]
    public function asMonthReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asMonth());
    }

    #[Test]
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

    #[Test]
    public function asMonthReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asMonth());
    }

    #[Test]
    public function asMonthAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asMonth();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    #[Test]
    public function asMonthReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asMonth());
    }

    #[Test]
    public function asDayAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asMonth();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asMonthReturnsValidValue(): void
    {
        $month = $this->readParam('2012-03-11')->asMonth();
        assertThat(
            $month !== null ? $month->asString() : null,
            equals('2012-03')
        );

    }

    #[Test]
    public function asMonthReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
            $this->readParam((new Month())->asString())
                ->asMonth(new DatespanRange(new Date('tomorrow'), null))
        );
    }

    #[Test]
    public function asMonthAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam((new Month())->asString())
            ->asMonth(new DatespanRange(new Date('tomorrow'), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
