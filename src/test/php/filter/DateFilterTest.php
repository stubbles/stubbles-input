<?php
declare(strict_types=1);
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
use stubbles\input\filter\range\DateRange;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
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
     * @type  DateFilter
     */
    private $dateFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dateFilter = DateFilter::instance();
        parent::setUp();
    }

    public function getEmptyValues(): array
    {
        return [[''], [null]];
    }

    /**
     * @param  scalar  $value
     * @test
     * @dataProvider  getEmptyValues
     */
    public function emptyParamsAreReturnedAsNull($value)
    {
        assertNull($this->dateFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDateInstance()
    {
        assert(
                $this->dateFilter->apply($this->createParam('2008-09-27')),
                equals(new Date('2008-09-27 00:00:00'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDate()
    {

        assertNull($this->dateFilter->apply($this->createParam('invalid date')));
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDate()
    {
        $param = $this->createParam('invalid date');
        $this->dateFilter->apply($param);
        assertTrue($param->hasError('DATE_INVALID'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = Date::now();
        assert(
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
    public function asDateReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asDate();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asDate();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDateReturnsValidValue()
    {
        assert(
                $this->readParam('2012-03-11')
                        ->asDate()
                        ->format('Y-m-d'),
                equals('2012-03-11')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsOutOfRange()
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
    public function asDateAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam((new Date('yesterday'))->asString())
             ->asDate(new DateRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
