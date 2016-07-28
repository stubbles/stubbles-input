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
use stubbles\date\span\Day;
use stubbles\input\filter\range\DatespanRange;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
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
     * @type  \stubbles\date\span\DatespanFilter
     */
    private $datespanFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->datespanFilter = DatespanFilter::instance();
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
        assertNull($this->datespanFilter->apply($this->createParam($value))[0]);
    }

    /**
     * @test
     */
    public function validParamsAreReturnedAsDayInstance()
    {
        assert(
                $this->datespanFilter->apply($this->createParam('2008-09-27'))[0],
                equals(new Day('2008-09-27'))
        );
    }

    /**
     * @test
     */
    public function applyReturnsNullForInvalidDay()
    {

        assertNull($this->datespanFilter->apply($this->createParam('invalid day'))[0]);
    }

    /**
     * @test
     */
    public function applyAddsErrorForInvalidDay()
    {
        $param = $this->createParam('invalid day');
        list($_, $errors) = $this->datespanFilter->apply($param);
        assertTrue(isset($errors['DATESPAN_INVALID']));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        assert(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asDatespan(),
                equals($default)
        );
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asDatespan();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asDatespan());
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asDatespan();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDatespanReturnsValidValue()
    {
        assert(
                $this->readParam('2012-03-11')
                        ->asDatespan()
                        ->format('Y-m-d'),
                equals('2012-03-11')
        );

    }

    /**
     * @test
     */
    public function asDatespanReturnsNullIfParamIsOutOfRange()
    {
        assertNull(
                $this->readParam('yesterday')
                        ->asDatespan(new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @test
     */
    public function asDatespanAddsParamErrorIfParamIsOutOfRange()
    {
        $this->readParam('yesterday')
             ->asDatespan(new DatespanRange(Date::now(), null));
        assertTrue($this->paramErrors->existFor('bar'));
    }
}
