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
use stubbles\date\span\Day;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
     * @var  \stubbles\input\filter\DatespanFilter
     */
    private $datespanFilter;

    protected function setUp(): void
    {
        $this->datespanFilter = DatespanFilter::instance();
        parent::setUp();
    }

    /**
     * @return  array<mixed[]>
     */
    public static function getEmptyValues(): array
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
        assertNull($this->datespanFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDayInstance(): void
    {
        assertThat(
                $this->datespanFilter->apply($this->createParam('2008-09-27'))[0],
                equals(new Day('2008-09-27'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDay(): void
    {

        assertNull($this->datespanFilter->apply($this->createParam('invalid day'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay(): void
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->datespanFilter->apply($param);
        assertTrue(isset($errors['DATESPAN_INVALID']));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndNotRequired(): void
    {
        assertNull($this->readParam(null)->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $default = new Day();
        assertThat(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDatespan(),
                equals($default)
        );
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asDatespan();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asDatespan();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsValidValue(): void
    {
        $datespan = $this->readParam('2012-03-11')->asDatespan();
        assertThat(
            $datespan !== null ? $datespan->asString() : null,
            equals('2012-03-11')
        );

    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsOutOfRange(): void
    {
        assertNull(
                $this->readParam('yesterday')
                        ->asDatespan(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsOutOfRange(): void
    {
        $this->readParam('yesterday')
             ->asDatespan(new DatespanRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
