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
use stubbles\input\filter\range\NumberRange;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\FloatFilter.
 *
 * @group  filter
 */
class FloatFilterTest extends FilterTest
{
    public function getValueResultTuples(): array
    {
        return [['8.4533', 8453],
                ['8.4538', 8453],
                ['8.45', 8450],
                ['8', 8000],
                [8.4533, 8453],
                [8.4538, 8453],
                [8.45, 8450],
                [8, 8000],
                [null, null],
                [true, 1000],
                [false, 0]
        ];
    }

    /**
     * @param  scalar  $value
     * @param  float   $expected
     * @test
     * @dataProvider  getValueResultTuples
     */
    public function value($value, $expected)
    {
        $floatFilter = new FloatFilter();
        assert(
                $floatFilter->setDecimals(3)->apply($this->createParam($value)),
                equals($expected)
        );
    }

    /**
     * @test
     */
    public function float()
    {
        $floatFilter = new FloatFilter();
        assert(
                $floatFilter->setDecimals(2)->apply($this->createParam('1.564')),
                equals(156)
        );
    }

    /**
     * @test
     */
    public function decimalsNotSet()
    {
        $floatFilter = new FloatFilter();
        assert($floatFilter->apply($this->createParam('1.564')), equals(1.564));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assert($this->readParam(null)->defaultingTo(3.03)->asFloat(), equals(3.03));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asFloat();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam(2.5)->asFloat(new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam(2.5)->asFloat(new NumberRange(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asFloatReturnsValidValue()
    {
        assert($this->readParam('3.13')->asFloat(), equals(3.13));
    }

    /**
     * @test
     */
    public function asFloatReturnsValidValueUsingDecimals()
    {
        assert($this->readParam('3.13')->asFloat(null, 2), equals(313));
    }
}
