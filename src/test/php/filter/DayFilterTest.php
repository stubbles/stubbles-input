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
use stubbles\date\span\Day;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\DayFilter.
 */
#[Group('filter')]
class DayFilterTest extends FilterTestBase
{
    private DayFilter $dayFilter;

    protected function setUp(): void
    {
        $this->dayFilter = DayFilter::instance();
        parent::setUp();
    }

    #[Test]
    #[DataProviderExternal(EmptyValuesDataProvider::class, 'provideStrings')]
    public function emptyParamsAreReturnedAsNull($value): void
    {
        assertNull($this->dayFilter->apply($this->createParam($value))[0]);
    }

    #[Test]
    public function validParamsAreReturnedAsDateInstance(): void
    {
        assertThat(
            $this->dayFilter->apply($this->createParam('2008-09-27'))[0],
            equals(new Day('2008-09-27'))
        );
    }

    #[Test]
    public function applyReturnsNullForInvalidDay(): void
    {

        assertNull($this->dayFilter->apply($this->createParam('invalid day'))[0]);
    }

    #[Test]
    public function applyAddsErrorForInvalidDay(): void
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->dayFilter->apply($param);
        assertTrue(isset($errors['DATE_INVALID']));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asDay());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $default = new Day();
        assertThat(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDay(),
                equals($default)
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asDay());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asDay();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asDay());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asDay();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asDayReturnsValidValue(): void
    {
        $day = $this->readParam('2012-03-11')->asDay();
        assertThat(
            $day !== null ? $day->format('Y-m-d') : null,
            equals('2012-03-11')
        );

    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
            $this->readParam('yesterday')
                ->asDay(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asDayAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam('yesterday')
            ->asDay(new DatespanRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
