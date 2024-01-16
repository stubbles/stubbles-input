<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use Generator;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
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
class DateFilterTest extends FilterTestBase
{
    private DateFilter $dateFilter;

    protected function setUp(): void
    {
        $this->dateFilter = DateFilter::instance();
        parent::setUp();
    }

    #[Test]
    #[DataProviderExternal(EmptyValuesDataProvider::class, 'provideStrings')]
    public function emptyParamsAreReturnedAsNull(?string $value): void
    {
        assertNull($this->dateFilter->apply($this->createParam($value))[0]);
    }

    #[Test]
    public function validParamsAreReturnedAsDateInstance(): void
    {
        assertThat(
            $this->dateFilter->apply($this->createParam('2008-09-27'))[0],
            equals(new Date('2008-09-27 00:00:00'))
        );
    }

    #[Test]
    public function applyReturnsNullForInvalidDate(): void
    {

        assertNull($this->dateFilter->apply($this->createParam('invalid date'))[0]);
    }

    #[Test]
    public function applyAddsErrorForInvalidDate(): void
    {
        $param = $this->createParam('invalid date');
        list($_, $errors) = $this->dateFilter->apply($param);
        assertTrue(isset($errors['DATE_INVALID']));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDateReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asDate());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
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
     */
    #[Test]
    public function asDateReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asDate());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDateAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asDate();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDateReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asDate());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDateAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asDate();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
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
     */
    #[Test]
    public function asDateReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
            $this->readParam((new Date('yesterday'))->asString())
                ->asDate(new DateRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDateAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam((new Date('yesterday'))->asString())
            ->asDate(new DateRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
